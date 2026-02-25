<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MikrotikConfig;
use App\Models\Reseller;
use App\Models\VoucherTemplate;
use App\Models\HotspotProfileMetadata;
use App\Models\BillingHistory;
use RouterOS\Client;
use RouterOS\Query;
use Exception;

class VoucherController extends Controller
{
    private function getClient($timeout = 10)
    {
        $mkConfig = MikrotikConfig::where('user_id', auth()->id())->first();
        if (!$mkConfig) return null;

        try {
            $client = new Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728),
                'timeout' => $timeout,
            ]);
            
            // Test connection with a simple query
            $client->query('/system/identity/print')->read();
            
            return $client;
        } catch (Exception $e) {
            \Log::error("RouterOS Connection Error: " . $e->getMessage());
            return null;
        }
    }

    // Helper: Generate Code (Ported from legacy functions.php)
    private function generateCode($length = 6, $prefix = '', $type = 'mixed') {
        $chars = [
            'numbers' => '23456789',
            'lowercase' => 'abcdefghijkmnpqrstuvwxyz23456789',
            'uppercase' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
            'mixed' => 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'
        ];
        
        $characters = $chars[$type] ?? $chars['mixed'];
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $prefix . $randomString;
    }

    public function index()
    {
        return redirect()->route('voucher.list');
    }

    public function generate(Request $request)
    {
        // Fetch resellers from CustomerMember table (type = 'Reseller')
        $resellers = \App\Models\CustomerMember::where('type', 'Reseller')->orderBy('name')->get();
        $templates = VoucherTemplate::orderBy('name')->get();
        $profiles = [];
        $serverProfiles = []; // Add this variable to avoid undefined variable error
        
        $client = $this->getClient();
        if ($client) {
            try {
                // Fetch User Profiles from RouterOS
                // Using print without proplist for now to get everything, or define specific props if needed
                $profiles = $client->query('/ip/hotspot/user/profile/print')->read();
                
                // Fetch Servers for dropdown
                $serverProfiles = $client->query('/ip/hotspot/print')->read();
            } catch (Exception $e) {
                // Handle connection error gracefully?
                session()->flash('error', 'Could not connect to RouterOS: ' . $e->getMessage());
            }
        }

        return view('vouchers.generate', compact('resellers', 'templates', 'profiles', 'serverProfiles'));
    }

    public function store(Request $request) 
    {
        // Increase time limit for mass generation
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $request->validate([
            'qty' => 'required|integer|min:1|max:1000', // Increased limit to 1000 since we use chunks
            'server' => 'required',
            'user_mode' => 'required|in:up,u+p',
            'user_length' => 'required|integer|min:3|max:12',
            'profile' => 'required',
        ]);

        $qty = (int)$request->qty;
        $server = $request->input('server', 'all');
        $userMode = $request->user_mode;
        $userLength = (int)$request->user_length;
        $prefix = $request->prefix ?? '';
        $charSet = $request->char_set ?? 'mixed';
        $profileName = $request->profile;
        $resellerId = $request->reseller_id;
        $timeLimit = $request->timelimit;
        $dataLimit = $request->datalimit;
        $templateId = $request->template_id;

        $mkConfig = MikrotikConfig::where('user_id', auth()->id())->first();
        $useRadius = $mkConfig ? $mkConfig->use_radius : false;

        $client = $this->getClient();
        if (!$client && !$useRadius) {
            return back()->withErrors(['connection' => 'Gagal terhubung ke MikroTik Router.'])->withInput();
        }

        // Get Reseller Name
        $resellerName = 'Admin';
        if ($resellerId) {
            $reseller = \App\Models\CustomerMember::find($resellerId);
            if ($reseller) $resellerName = $reseller->name;
        }

        $comment = "VC " . date('d/m/Y') . " [$resellerName]";
        
        // Metadata
        $meta = HotspotProfileMetadata::where('profile_name', $profileName)->first();
        $price = $meta ? $meta->price : 0;
        $validity = $meta ? $meta->validity : '-';
        $sellingPrice = $meta ? $meta->selling_price : $price;
        $dnsName = 'hotspot.mikhmon'; 
        $batchId = 'BATCH-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        // Balance Check for mitra-reseller
        if (auth()->user()->role === 'mitra-reseller') {
            $totalCost = $price * $qty; // Reseller pays modal/price
            $resellerProfile = \App\Models\Reseller::where('user_id', auth()->id())->first();
            if (!$resellerProfile || $resellerProfile->balance < $totalCost) {
                return back()->with('error', 'Saldo tidak mencukupi untuk generate ' . $qty . ' voucher. Saldo Anda: Rp ' . number_format($resellerProfile ? $resellerProfile->balance : 0) . ', Total Harga: Rp ' . number_format($totalCost) . '.')->withInput();
            }
        }

        $totalProcessed = 0;
        $chunkSize = 100; // Process 100 vouchers at a time

        \Log::info("Starting chunked voucher generation", ['qty' => $qty, 'chunk_size' => $chunkSize]);

        for ($i = 0; $i < $qty; $i += $chunkSize) {
            $currentChunkSize = min($chunkSize, $qty - $i);
            $dbDataChunk = [];
            $mkScriptChunk = "";

            for ($j = 0; $j < $currentChunkSize; $j++) {
                $code = $this->generateCode($userLength, $prefix, $charSet);
                $pass = ($userMode == 'up') ? $code : $this->generateCode($userLength, '', $charSet);

                // Build MikroTik Command
                $cmd = "/ip hotspot user add server=\"$server\" name=\"$code\" password=\"$pass\" profile=\"$profileName\" comment=\"$comment\"";
                if ($timeLimit) $cmd .= " limit-uptime=\"$timeLimit\"";
                if ($dataLimit) $cmd .= " limit-bytes-total=\"$dataLimit\"";
                $mkScriptChunk .= $cmd . ";\n";

                // Build DB Record
                $dbDataChunk[] = [
                    'user_id' => auth()->id(),
                    'voucher_code' => $code,
                    'username' => $code,
                    'password' => $pass,
                    'profile' => $profileName,
                    'price' => $price,
                    'selling_price' => $sellingPrice,
                    'validity' => $validity,
                    'hotspotname' => auth()->user()->name,
                    'timelimit' => $timeLimit,
                    'datalimit' => $dataLimit,
                    'reseller_id' => $resellerId,
                    'template_id' => $templateId,
                    'batch_id' => $batchId,
                    'generated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            try {
                // 1. Send Chunk to MikroTik (Skip if using RADIUS)
                if (!$useRadius && $client) {
                    foreach ($dbDataChunk as $u) {
                        try {
                            $cmd = new \RouterOS\Query('/ip/hotspot/user/add');
                            $cmd->equal('server', $server);
                            $cmd->equal('name', $u['username']);
                            $cmd->equal('password', $u['password']);
                            $cmd->equal('profile', $u['profile']);
                            $cmd->equal('comment', $comment);
                            if (!empty($u['timelimit'])) $cmd->equal('limit-uptime', $u['timelimit']);
                            if (!empty($u['datalimit'])) $cmd->equal('limit-bytes-total', $u['datalimit']);
                            
                            $client->query($cmd)->read();
                        } catch (\Exception $e) {
                            \Log::error("Failed to add user to MikroTik", ['user' => $u['username'], 'error' => $e->getMessage()]);
                        }
                    }
                }

                // 2. Insert Chunk to Database (Using transaction for safety)
                DB::transaction(function() use ($dbDataChunk) {
                    BillingHistory::insert($dbDataChunk);
                });
                
                $totalProcessed += $currentChunkSize;
                \Log::info("Chunk processed", ['count' => $totalProcessed]);

            } catch (Exception $e) {
                \Log::error("Chunk failed at offset $i", ['error' => $e->getMessage()]);
            }
        }

        // Deduct balance for mitra-reseller
        if (auth()->user()->role === 'mitra-reseller' && $totalProcessed > 0) {
            $totalCostToDeduct = $price * $totalProcessed; // deduct modal
            $resellerProfile = \App\Models\Reseller::where('user_id', auth()->id())->first();
            if ($resellerProfile) {
                $before = $resellerProfile->balance;
                $resellerProfile->decrement('balance', $totalCostToDeduct);
                \App\Models\BalanceHistory::create([
                    'user_id' => auth()->id(),
                    'customer_id' => auth()->id(),
                    'type' => 'OUT',
                    'amount' => $totalCostToDeduct,
                    'before_balance' => $before,
                    'after_balance' => $before - $totalCostToDeduct,
                    'description' => "Bayar Generate $totalProcessed Voucher ($profileName)",
                    'reference_id' => $batchId,
                ]);
            }
        }

        return redirect()->route('voucher.distribution')
            ->with('success', "Voucher {$batchId} sejumlah {$totalProcessed} Berhasil di generate");
    }

    // --- Voucher List Module ---
    public function list()
    {
        $userId = auth()->id();
        $mkConfig = \App\Models\MikrotikConfig::where('user_id', $userId)->first();
        $useRadius = $mkConfig ? $mkConfig->use_radius : false;

        $cacheKey = "hotspot_users_{$userId}";
        
        // Caching selama 5 menit agar tidak terus-menerus nanya ke MikroTik
        $data = \Cache::remember($cacheKey, 300, function() use ($useRadius, $userId) {
            $client = $this->getClient();
            $users = [];
            $profiles = [];
            
            if ($useRadius) {
                // Fetch from Local DB for RADIUS Mode
                $dbUsers = \App\Models\BillingHistory::where('user_id', $userId)
                    ->orderBy('id', 'desc')
                    ->get();
                
                foreach ($dbUsers as $u) {
                    $users[] = [
                        '.id' => (string)$u->id,
                        'name' => $u->username ?? $u->voucher_code,
                        'password' => $u->password,
                        'profile' => $u->profile,
                        'limit-uptime' => $u->timelimit,
                        'server' => $u->server ?? 'all',
                        'comment' => $u->comment ?? ($u->batch_id ? "Batch: {$u->batch_id}" : "-"),
                        'is_radius' => true
                    ];
                }
            }

            if ($client) {
                try {
                    if (!$useRadius) {
                        $users = $client->query('/ip/hotspot/user/print')->read();
                    }
                    $profiles = $client->query('/ip/hotspot/user/profile/print')->read();
                } catch (\Exception $e) {
                    \Log::error("RouterOS Error: " . $e->getMessage());
                }
            }
            return compact('users', 'profiles');
        });

        $users = $data['users'] ?? [];
        $profiles = $data['profiles'] ?? [];
        
        return view('vouchers.list', compact('users', 'profiles'));
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'id' => 'required', // MikroTik ID (.id)
            'name' => 'required',
            'password' => 'required',
            'profile' => 'required',
        ]);

        $mkConfig = \App\Models\MikrotikConfig::where('user_id', auth()->id())->first();
        $useRadius = $mkConfig ? $mkConfig->use_radius : false;

        if ($useRadius) {
            $voucher = \App\Models\BillingHistory::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->first();
            
            if (!$voucher) return back()->with('error', 'Voucher tidak ditemukan di database.');
            
            $voucher->update([
                'username' => $request->name,
                'voucher_code' => $request->name,
                'password' => $request->password,
                'profile' => $request->profile,
                'timelimit' => $request->limit_uptime,
                'comment' => $request->comment
            ]);
            
            return back()->with('success', 'User updated successfully (Mode RADIUS).');
        }

        $client = $this->getClient();
        if (!$client) {
            return back()->with('error', 'RouterOS Connection Failed');
        }

        try {
            // 1. Fetch current data from MikroTik to identify the 'old name' before update
            // This is needed to sync renaming with our local database (billing_history)
            $oldUser = $client->query('/ip/hotspot/user/print', ['.id' => $request->id])->read();
            $oldName = !empty($oldUser) ? ($oldUser[0]['name'] ?? null) : null;

            // 2. Prepare properties to SET (mandatory fields + non-empty optional ones)
            $updateData = [
                '.id' => $request->id,
                'name' => $request->name,
                'password' => $request->password,
                'profile' => $request->profile,
            ];
            
            if ($request->filled('comment')) $updateData['comment'] = $request->comment;
            if ($request->filled('email')) $updateData['email'] = $request->email;
            
            // Handle limit-uptime: If filled, add to SET. If empty, we must UNSET it separately.
            if ($request->filled('limit_uptime')) {
                $updateData['limit-uptime'] = $request->limit_uptime;
            }

            // Execute SET command
            $client->query('/ip/hotspot/user/set', $updateData)->read();

            // 3. SYNC WITH DATABASE: If name changed, update billing_history
            if ($oldName && $oldName !== $request->name) {
                // If this was a "Lokal" voucher, we must update its name in DB so it doesn't become "Asing"
                BillingHistory::where('username', $oldName)
                    ->where('user_id', auth()->id())
                    ->update([
                        'username' => $request->name,
                        'voucher_code' => $request->name
                    ]);
                
                \Log::info("Voucher renamed in DB", ['old' => $oldName, 'new' => $request->name]);
            }
            
            // 2. Handle Unsets (Fields that were cleared)
            // If limit_uptime is explicitly empty, we unset it to make it unlimited.
            if (!$request->filled('limit_uptime')) {
                $client->query('/ip/hotspot/user/unset', [
                    '.id' => $request->id,
                    'value-name' => 'limit-uptime'
                ])->read();
            }
            
            // Note: If comment/email are cleared in the UI, we might want to unset them too?
            // For now, let's focus on limit-uptime as requested.
            // If the user clears the comment, $request->filled('comment') is false.
            // The previous 'set' logic didn't unset it either. 
            // Better behavior: If comment/email are present in $request (even if empty string?), set them to empty?
            // RouterOS doesn't like empty strings for some props, requires unset.
            // Let's improve the comment handling too for completeness.

            if (!$request->filled('comment')) {
                 $client->query('/ip/hotspot/user/unset', [
                    '.id' => $request->id,
                    'value-name' => 'comment'
                ])->read();
            }

            return back()->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $mkConfig = MikrotikConfig::where('user_id', auth()->id())->first();
        $useRadius = $mkConfig ? $mkConfig->use_radius : false;

        if ($useRadius) {
            // If using RADIUS, we only need to delete from local DB
            // We need to find the username first if we only have the ID (though usually ID is for Mikrotik)
            // Wait, in list() for RADIUS, we might not even have Mikrotik IDs.
            // Let's assume for now delete from DB is enough if ID is handled.
            // But wait, the $id passed here is usually Mikrotik's .id.
            // If using RADIUS, the list might be different.
        }

        $client = $this->getClient();
        if (!$client && !$useRadius) {
            return back()->with('error', 'Gagal menghubungkan ke MikroTik. Pastikan router online.');
        }

        try {
            $username = null;
            
            if ($useRadius) {
                // In RADIUS mode, ID is from DB
                $voucher = \App\Models\BillingHistory::where('id', $id)
                    ->where('user_id', auth()->id())
                    ->first();
                
                if ($voucher) {
                    $username = $voucher->username;
                    $voucher->delete();
                } else {
                    return back()->with('error', 'Voucher tidak ditemukan di database.');
                }
            } else {
                $client = $this->getClient();
                if (!$client) {
                    return back()->with('error', 'Gagal menghubungkan ke MikroTik. Pastikan router online.');
                }

                // Fetch user first to get username for DB sync
                $qPrint = new \RouterOS\Query('/ip/hotspot/user/print');
                $qPrint->where('.id', $id);
                $user = $client->query($qPrint)->read();
                $username = !empty($user) ? ($user[0]['name'] ?? null) : null;

                if (empty($user)) {
                    return back()->with('error', 'Voucher tidak ditemukan di MikroTik.');
                }

                // Remove from MikroTik
                try {
                    $qRemove = new \RouterOS\Query('/ip/hotspot/user/remove');
                    $qRemove->add('=.id=' . $id);
                    $client->query($qRemove)->read();
                } catch (\Exception $e) {
                    $msg = $e->getMessage();
                    if (!str_contains($msg, 'Undefined array key') && 
                        !str_contains($msg, 'offset 0') && 
                        !str_contains($msg, 'array key 0')) {
                        throw $e;
                    }
                }

                // If this user was in our records, delete it too
                if ($username) {
                    \App\Models\BillingHistory::where('username', $username)
                        ->where('user_id', auth()->id())
                        ->delete();
                }
            }

            return back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // --- Voucher Profiles Module ---
    public function profiles()
    {
        $profiles = [];
        $client = $this->getClient();

        if ($client) {
            try {
                // Fetch profiles from RouterOS
                $profiles = $client->query('/ip/hotspot/user/profile/print')->read();
                
                // Merge with local metadata if exists (price, validity, selling_price)
                foreach ($profiles as &$prof) {
                    $meta = HotspotProfileMetadata::where('profile_name', $prof['name'])->first();
                    $prof['local_metadata'] = $meta;
                }
            } catch (Exception $e) {
                session()->flash('error', 'RouterOS Error: ' . $e->getMessage());
            }
        }

        return view('vouchers.profiles', compact('profiles'));
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'shared_users' => 'required|integer',
            'rate_limit' => 'nullable|string',
        ]);

        $client = $this->getClient();
        if (!$client) {
            return back()->with('error', 'RouterOS Connection Failed');
        }

        try {
            // Add/Update Profile in RouterOS
            // Using explicit Query object construction like in generate() to be safe
            $query = new \RouterOS\Query('/ip/hotspot/user/profile/add');
            $query->add('=name=' . $request->name);
            $query->add('=shared-users=' . $request->shared_users);
            
            if ($request->rate_limit) {
                $query->add('=rate-limit=' . $request->rate_limit);
            }
            
            // Fix: Map 'validity' to 'session-timeout' if provided
            if ($request->validity) {
                $query->add('=session-timeout=' . $request->validity);
                // Optional: Also set mac-cookie-timeout or similar if needed, but session-timeout is the main one.
            }

            try {
                $client->query($query)->read();
            } catch (\Exception $e) {
                // If error is just about empty response/array key (common with add/set), ignore it
                if (!str_contains($e->getMessage(), 'Undefined array key')) {
                    throw $e;
                }
            }

            // Save Metadata to DB
            HotspotProfileMetadata::updateOrCreate(
                ['profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->sell_price ?? 0,
                    'validity' => $request->validity ?? '1d'
                ]
            );

            return back()->with('success', 'Profile created successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to create profile: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'shared_users' => 'required|integer',
            'rate_limit' => 'nullable|string',
            'validity' => 'nullable|string',
            'price' => 'nullable|numeric',
            'sell_price' => 'nullable|numeric',
        ]);

        $client = $this->getClient();
        if (!$client) {
            return back()->with('error', 'RouterOS Connection Failed');
        }

        try {
            // Update in MikroTik
            // Using explicit Query object construction for stability
            $query = new \RouterOS\Query('/ip/hotspot/user/profile/set');
            $query->add('=.id=' . $request->id);
            $query->add('=shared-users=' . (string)$request->shared_users);
            
            if ($request->rate_limit) {
                $query->add('=rate-limit=' . $request->rate_limit);
            }

            // Fix: Map 'validity' to 'session-timeout'
            if ($request->validity) {
                $query->add('=session-timeout=' . $request->validity);
            }
            
            // DEBUG: Log request data
            \Log::info('UpdateProfile Request', [
                'id' => $request->id,
                'name' => $request->name,
                'query' => $query
            ]);
            
            // Execute the set command
            try {
                $client->query($query)->read();
            } catch (\Exception $mikrotikError) {
                \Log::error('MikroTik Error', [
                    'message' => $mikrotikError->getMessage(),
                ]);
                
                // If error is just about empty response, ignore it
                if (!str_contains($mikrotikError->getMessage(), 'Undefined array key')) {
                    throw $mikrotikError;
                }
            }
            
            // Update metadata in DB
            HotspotProfileMetadata::updateOrCreate(
                ['profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->sell_price ?? 0,
                    'validity' => $request->validity ?? '1d'
                ]
            );
            
            \Log::info('Profile updated successfully', ['name' => $request->name]);
            
            return back()->with('success', 'Profile updated successfully.');
        } catch (Exception $e) {
            \Log::error('UpdateProfile Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function deleteProfile(Request $request)
    {
        $id = $request->query('id'); // Mikrotik ID
        $name = $request->query('name'); // Profile Name
        
        $client = $this->getClient();
        if ($client) {
            try {
                $query = new \RouterOS\Query('/ip/hotspot/user/profile/remove');
                $query->add('=.id=' . $id);
                
                try {
                    $client->query($query)->read();
                } catch (\Exception $e) {
                    if (!str_contains($e->getMessage(), 'Undefined array key')) {
                        throw $e;
                    }
                }
                
                // Remove metadata
                if ($name) {
                    HotspotProfileMetadata::where('profile_name', $name)->delete();
                }
                
                return back()->with('success', 'Profile deleted successfully.');
            } catch (Exception $e) {
                return back()->with('error', 'Failed to delete: ' . $e->getMessage());
            }
        }
        return back()->with('error', 'RouterOS Connection Failed');
    }


    // --- Template Manager Module ---
    public function templates()
    {
        $templates = \App\Models\VoucherTemplate::all();
        return view('vouchers.templates', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'html_content' => 'required',
        ]);

        // Simple CSS extraction (very basic)
        $html = $request->html_content;
        $css = '';
        
        // Try to extract content inside <style> tags
        if (preg_match('/<style[^>]*>(.*?)<\/style>/is', $html, $matches)) {
            $css = $matches[1];
            // Remove style tag from html to avoid duplication if needed, or keep it.
            // keeping it is fine for now as we just render the html string.
        }

        VoucherTemplate::create([
            'name' => $request->name,
            'html_content' => $html,
            'css_content' => $css // Optional separate storage
        ]);

        return back()->with('success', 'Template saved successfully.');
    }

    public function deleteTemplate($id)
    {
        $template = \App\Models\VoucherTemplate::findOrFail($id);
        
        if ($template->user_id === null || $template->is_system) {
            return back()->with('error', 'Default system templates cannot be deleted.');
        }

        $template->delete();
        return back()->with('success', 'Template deleted successfully.');
    }

    // REPORT LOGIC: Voucher Sold (Detail)
    public function sold(Request $request)
    {
        $salesToday = 0;
        $salesMonth = 0;
        $countToday = 0;
        $countMonth = 0;
        $vouchers = collect();
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');

        $client = $this->getClient();
        $serverDateTime = null;
        
        if ($client) {
            try {
                // Get server time from MikroTik for accuracy
                $systemClock = $client->query('/system/clock/print')->read();
                
                if (!empty($systemClock)) {
                    $serverDate = $systemClock[0]['date'] ?? null;
                    $serverTime = $systemClock[0]['time'] ?? null;
                    
                    if ($serverDate && $serverTime) {
                        try {
                            $serverDateTime = \Carbon\Carbon::createFromFormat('M/d/Y H:i:s', $serverDate . ' ' . $serverTime);
                        } catch (\Exception $e) {
                            $serverDateTime = now();
                        }
                    }
                }
                
                if (!$serverDateTime) {
                    $serverDateTime = now();
                }
                
                // Get ACTIVE users from MikroTik
                $activeUsers = $client->query('/ip/hotspot/active/print')->read();
                $activeUsernames = array_filter(array_column($activeUsers, 'user'));
                
                if (!empty($activeUsernames)) {
                    // Optimized: Update all at once where first_login_at is null
                    BillingHistory::whereIn('username', $activeUsernames)
                        ->where('user_id', auth()->id())
                        ->whereNull('first_login_at')
                        ->update(['first_login_at' => $serverDateTime]);
                }
                
            } catch (\Exception $e) {
                \Log::error("Error tracking active users", ['error' => $e->getMessage()]);
            }
        }
        
        // Base Query
        $query = DB::table('billing_history as bh')
            ->leftJoin('customer_members as cm', 'bh.reseller_id', '=', 'cm.id')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->select(
                'bh.*', 
                'cm.name as reseller_name', 
                'pm.price as current_meta_price',
                DB::raw('COALESCE(pm.price, bh.price) as actual_price')
            )
            ->where('bh.user_id', auth()->id())
            ->whereNotNull('bh.first_login_at');

        // Apply Date Range Filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween('bh.first_login_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('bh.voucher_code', 'like', "%{$search}%")
                  ->orWhere('bh.profile', 'like', "%{$search}%")
                  ->orWhere('cm.name', 'like', "%{$search}%");
            });
        }

        $vouchers = $query->orderBy('bh.first_login_at', 'desc')
            ->paginate(50);
                
        // Calculate revenue based on router time (if available) or server time
        $anchorDate = $serverDateTime ? $serverDateTime : now();
        $today = $anchorDate->format('Y-m-d');
        $thisMonth = $anchorDate->month;
        $thisYear = $anchorDate->year;
        
        // Today's sales
        $todaySales = DB::table('billing_history as bh')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->whereDate('bh.first_login_at', $today)
            ->where('bh.user_id', auth()->id())
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(COALESCE(pm.price, bh.price)) as total')
            )
            ->first();
        
        $salesToday = $todaySales->total ?? 0;
        $countToday = $todaySales->count ?? 0;
        
        // This month's sales
        $monthSales = DB::table('billing_history as bh')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->whereYear('bh.first_login_at', $thisYear)
            ->whereMonth('bh.first_login_at', $thisMonth)
            ->where('bh.user_id', auth()->id())
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(COALESCE(pm.price, bh.price)) as total')
            )
            ->first();
        
        $salesMonth = $monthSales->total ?? 0;
        $countMonth = $monthSales->count ?? 0;
        
        // Filtered Total (If filter applied)
        $filteredTotal = 0;
        if ($startDate && $endDate) {
            $filteredTotal = $vouchers->sum('actual_price');
        }

        // Grand Total (all time sold vouchers)
        $grandTotal = DB::table('billing_history as bh')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->whereNotNull('bh.first_login_at')
            ->where('bh.user_id', auth()->id())
            ->sum(DB::raw('COALESCE(pm.price, bh.price)'));

        return view('vouchers.sold', compact('vouchers', 'grandTotal', 'salesToday', 'salesMonth', 'countToday', 'countMonth', 'startDate', 'endDate', 'filteredTotal', 'search'));
    }

    public function exportSold(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Silakan pilih rentang tanggal.');
        }

        $query = DB::table('billing_history as bh')
            ->leftJoin('customer_members as cm', 'bh.reseller_id', '=', 'cm.id')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->select(
                'bh.first_login_at',
                'bh.voucher_code',
                'bh.profile',
                'cm.name as reseller_name',
                DB::raw('COALESCE(pm.price, bh.price) as price')
            )
            ->where('bh.user_id', auth()->id())
            ->whereNotNull('bh.first_login_at')
            ->whereBetween('bh.first_login_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('bh.voucher_code', 'like', "%{$search}%")
                  ->orWhere('bh.profile', 'like', "%{$search}%")
                  ->orWhere('cm.name', 'like', "%{$search}%");
            });
        }
            
        $vouchers = $query->orderBy('bh.first_login_at', 'asc')->get();

        $filename = "rekap_voucher_" . $startDate . "_to_" . $endDate . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Waktu Login', 'Kode Voucher', 'Profile', 'Reseller/Admin', 'Harga'];

        $callback = function() use($vouchers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $total = 0;
            foreach ($vouchers as $v) {
                fputcsv($file, [
                    $v->first_login_at,
                    $v->voucher_code,
                    $v->profile,
                    $v->reseller_name ?? 'Admin',
                    $v->price
                ]);
                $total += $v->price;
            }

            fputcsv($file, ['', '', '', 'TOTAL', $total]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // REPORT LOGIC: Voucher Recap (Summary)
    public function recap()
    {
        // Logic from voucher_recap_logic.php
        // Group by Date
        $recapData = DB::table('billing_history as bh')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->select(
                DB::raw('DATE(bh.date_sold) as sale_date'),
                DB::raw('COUNT(*) as qty'),
                DB::raw('SUM(COALESCE(pm.price, bh.price)) as total_income')
            )
            ->where('bh.user_id', auth()->id()) // SECURITY FIX
            ->groupBy(DB::raw('DATE(bh.date_sold)'))
            ->orderBy('sale_date', 'desc')
            ->limit(30)
            ->get();

        // Fetch MikroTik Scripts (Reports from Scripts)
        $mikrotikReports = [];
        $client = $this->getClient();

        if ($client) {
            try {
                $currentYear = date('Y');
                $lastYear = $currentYear - 1;

                // Mikrotik Query: /system/script/print where comment=mikhmon and owner~Year
                // Simplified fetching
                $scripts = $client->query('/system/script/print')->read(); // Filter in PHP as previously decided

                $mikrotikReports = [
                    $currentYear => [],
                    $lastYear => []
                ];

                foreach ($scripts as $script) {
                    // Check for comment 'mikhmon'
                    if (isset($script['comment']) && $script['comment'] === 'mikhmon') {
                        $owner = $script['owner'] ?? '';
                        
                         // Simple logic to categorize by year
                        if (str_ends_with($owner, $currentYear)) {
                            $mikrotikReports[$currentYear][] = $script;
                        } elseif (str_ends_with($owner, $lastYear)) {
                            $mikrotikReports[$lastYear][] = $script;
                        }
                    }
                }

            } catch (Exception $e) {
                // Ignore API errors for reports
            }
        }

        return view('vouchers.recap', compact('recapData', 'mikrotikReports'));
    }
    
    // Reseller Management
    public function resellers() {
        $resellers = Reseller::orderBy('name')->get();
        return view('vouchers.resellers', compact('resellers'));
    }
    
    public function storeReseller(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'balance' => 'nullable|numeric',
        ]);
        
        if ($request->filled('id')) {
            Reseller::where('id', $request->id)->update($validated);
            $msg = 'Reseller diperbarui.';
        } else {
            Reseller::create($validated);
            $msg = 'Reseller ditambahkan.';
        }
        return redirect()->back()->with('success', $msg);
    }
    
    public function deleteReseller($id) {
        Reseller::destroy($id);
        return redirect()->back()->with('success', 'Reseller dihapus.');
    }
    
    // Voucher Distribution
    public function distribution() {
        // Get voucher distribution data from billing_history (aggregated by reseller)
        $distributions = DB::table('billing_history as bh')
            ->leftJoin('customer_members as cm', 'bh.reseller_id', '=', 'cm.id')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->select(
                'bh.reseller_id',
                'cm.name as reseller_name',
                'bh.profile',
                DB::raw('COALESCE(pm.price, bh.price) as price'),
                DB::raw('COUNT(*) as total_vouchers'),
                DB::raw('(COUNT(*) * COALESCE(pm.price, bh.price)) as total_amount'),
                DB::raw('MAX(bh.payment_status) as payment_status'),
                DB::raw('MAX(bh.paid_at) as paid_at')
            )
            ->whereNotNull('bh.reseller_id')
            ->where('cm.type', 'Reseller')
            ->where('bh.user_id', auth()->id()) // SECURITY FIX
            ->groupBy('bh.reseller_id', 'cm.name', 'bh.profile', 'pm.price', 'bh.price')
            ->orderBy('cm.name')
            ->get();
        
        // Get generation batches (new vouchers generated with batch tracking)
        $batches = DB::table('billing_history as bh')
            ->leftJoin('customer_members as cm', 'bh.reseller_id', '=', 'cm.id')
            ->leftJoin('voucher_templates as vt', 'bh.template_id', '=', 'vt.id')
            ->select(
                'bh.batch_id',
                DB::raw('MAX(bh.generated_at) as generated_at'),
                DB::raw('MAX(cm.name) as reseller_name'),
                DB::raw('MAX(bh.profile) as profile'),
                DB::raw('MAX(vt.name) as template_name'),
                DB::raw('COUNT(*) as qty'),
                DB::raw('SUM(bh.price) as total_price')
            )
            ->whereNotNull('bh.batch_id')
            ->where('bh.user_id', auth()->id()) // SECURITY FIX
            ->groupBy('bh.batch_id')
            ->orderBy('generated_at', 'desc')
            ->limit(50)
            ->get();
        
        // Get reseller list for filter
        $resellers = DB::table('customer_members')
            ->where('type', 'Reseller')
            ->orderBy('name')
            ->get();
            
        $templates = \App\Models\VoucherTemplate::orderBy('name')->get();
            
        return view('vouchers.distribution', compact('distributions', 'batches', 'resellers', 'templates'));
    }
    
    public function printBatch(Request $request, $batchId) {
        // Fetch vouchers from this batch
        $vouchers = BillingHistory::where('batch_id', $batchId)->get();
        
        if ($vouchers->isEmpty()) {
            return redirect()->route('voucher.distribution')->with('error', 'Batch tidak ditemukan');
        }
        
        // Get template from request or fallback
        $template = null;
        $templateId = $request->template_id ?: $vouchers->first()->template_id;
        if ($templateId) {
            $template = \App\Models\VoucherTemplate::find($templateId);
        }
        
        // Get batch info
        $batchInfo = DB::table('billing_history as bh')
            ->leftJoin('customer_members as cm', 'bh.reseller_id', '=', 'cm.id')
            ->where('bh.batch_id', $batchId)
            ->select('cm.name as reseller_name', 'bh.profile', 'bh.generated_at')
            ->first();
        
        return view('vouchers.print_vouchers', compact('vouchers', 'template', 'batchInfo'));
    }
    
    // View batch details
    public function viewBatch($batchId) {
        $vouchers = BillingHistory::where('batch_id', $batchId)->get();
        
        if ($vouchers->isEmpty()) {
            return redirect()->route('voucher.distribution')->with('error', 'Batch tidak ditemukan');
        }
        
        $batchInfo = DB::table('billing_history as bh')
            ->leftJoin('customer_members as cm', 'bh.reseller_id', '=', 'cm.id')
            ->leftJoin('voucher_templates as vt', 'bh.template_id', '=', 'vt.id')
            ->where('bh.batch_id', $batchId)
            ->select('cm.name as reseller_name', 'bh.profile', 'bh.generated_at', 'vt.name as template_name')
            ->first();
        
        return view('vouchers.view_batch', compact('vouchers', 'batchInfo', 'batchId'));
    }
    
    // Delete batch
    public function deleteBatch($batchId) {
        $vouchers = BillingHistory::where('batch_id', $batchId)
            ->where('user_id', auth()->id())
            ->get();
        
        if ($vouchers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan di database.'], 404);
        }
        
        $client = $this->getClient();
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Gagal koneksi ke MikroTik.'], 500);
        }

        $deletedCount = 0;
        $foundIds = [];
        
        try {
            // Fetch ALL users once to map names to IDs
            $allUsers = $client->query('/ip/hotspot/user/print')->read();
            $mkUsersMap = [];
            foreach ($allUsers as $u) {
                if (isset($u['name']) && isset($u['.id'])) {
                    $mkUsersMap[$u['name']] = $u['.id'];
                }
            }
            
            foreach ($vouchers as $voucher) {
                if (isset($mkUsersMap[$voucher->username])) {
                    $foundIds[] = $mkUsersMap[$voucher->username];
                }
            }
            
            if (!empty($foundIds)) {
                try {
                    // Optimized: Delete multiple IDs at once with Query object
                    $q = new \RouterOS\Query('/ip/hotspot/user/remove');
                    $q->add('=.id=' . implode(',', $foundIds));
                    $client->query($q)->read();
                    $deletedCount = count($foundIds);
                } catch (\Exception $e) {
                    $msg = $e->getMessage();
                    if (str_contains($msg, 'Undefined array key') || 
                        str_contains($msg, 'offset 0') || 
                        str_contains($msg, 'array key 0')) {
                        $deletedCount = count($foundIds);
                    } else {
                         return response()->json(['success' => false, 'message' => 'Gagal hapus MikroTik: ' . $msg], 500);
                    }
                }
            }

            // Sync with database
            BillingHistory::where('batch_id', $batchId)
                ->where('user_id', auth()->id())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Batch berhasil dihapus. {$deletedCount} voucher dihapus dari MikroTik.",
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    // View distribution details
    public function viewDistribution(Request $request) {
        $resellerId = $request->query('reseller_id');
        $profile = $request->query('profile');
        
        $vouchers = DB::table('billing_history as bh')
            ->leftJoin('customer_members as cm', 'bh.reseller_id', '=', 'cm.id')
            ->where('bh.reseller_id', $resellerId)
            ->where('bh.profile', $profile)
            ->select('bh.*', 'cm.name as reseller_name')
            ->get();
        
        if ($vouchers->isEmpty()) {
            return redirect()->route('voucher.distribution')->with('error', 'Data tidak ditemukan');
        }
        
        $resellerName = $vouchers->first()->reseller_name;
        
        return view('vouchers.view_distribution', compact('vouchers', 'resellerName', 'profile'));
    }
    
    // Delete distribution
    public function deleteDistribution(Request $request) {
        $resellerId = $request->input('reseller_id');
        $profile = $request->input('profile');
        
        $vouchers = BillingHistory::where('reseller_id', $resellerId)
            ->where('profile', $profile)
            ->where('user_id', auth()->id())
            ->get();
        
        if ($vouchers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan di database.'], 404);
        }
        
        $client = $this->getClient();
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Gagal koneksi ke MikroTik.'], 500);
        }

        $deletedCount = 0;
        $foundIds = [];

        try {
            $allUsers = $client->query('/ip/hotspot/user/print')->read();
            $mkUsersMap = [];
            foreach ($allUsers as $u) {
                if (isset($u['name'])) $mkUsersMap[$u['name']] = $u['.id'];
            }

            foreach ($vouchers as $voucher) {
                if (isset($mkUsersMap[$voucher->username])) {
                    $foundIds[] = $mkUsersMap[$voucher->username];
                }
            }

            if (!empty($foundIds)) {
                try {
                    $q = new \RouterOS\Query('/ip/hotspot/user/remove');
                    $q->add('=.id=' . implode(',', $foundIds));
                    $client->query($q)->read();
                    $deletedCount = count($foundIds);
                } catch (\Exception $e) {
                    $msg = $e->getMessage();
                    if (str_contains($msg, 'Undefined array key') || 
                        str_contains($msg, 'offset 0') || 
                        str_contains($msg, 'array key 0')) {
                        $deletedCount = count($foundIds);
                    } else {
                        throw $e;
                    }
                }
            }

            if ($deletedCount === 0 && $vouchers->count() > 0) {
                 return response()->json(['success' => false, 'message' => 'Gagal menghapus! Data ditemukan di DB tetapi tidak ditemukan di MikroTik.'], 400);
            }

            // Sync with database
            BillingHistory::where('reseller_id', $resellerId)
                ->where('profile', $profile)
                ->where('user_id', auth()->id())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Distribusi berhasil dihapus. {$deletedCount} voucher dihapus dari MikroTik."
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'RouterOS Error: ' . $e->getMessage()], 500);
        }
    }
    
    // Mark distribution as paid
    public function markPaid(Request $request) {
        $resellerId = $request->input('reseller_id');
        $profile = $request->input('profile');
        
        $updated = BillingHistory::where('reseller_id', $resellerId)
            ->where('profile', $profile)
            ->update([
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);
        
        if ($updated > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diupdate menjadi Lunas'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal update status pembayaran'
        ], 400);
    }

}
