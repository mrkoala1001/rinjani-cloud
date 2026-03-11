<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MikrotikConfig;

class HotSupportController extends Controller
{
    public function index()
    {
        // Global Stats
        $totalOwners = User::whereIn('role', ['owner', 'mitra', 'mitra-reseller'])->where('created_by', auth()->id())->count();
        $totalRouters = MikrotikConfig::whereIn('user_id', function($query) {
            $query->select('id')->from('users')->where('created_by', auth()->id());
        })->count();
        
        // Income Stats (Aggregation)
        // Note: We MUST filter by managed owners (created_by this ISP)
        $managedOwnerIds = function($query) {
            $query->select('id')->from('users')->where('created_by', auth()->id());
        };
        
        // Global Stats for ISP
        $totalVoucherCreated = \App\Models\BillingHistory::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->whereIn('user_id', $managedOwnerIds)
            ->sum('price') ?? 0;

        $totalVoucherSold = \App\Models\BillingHistory::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->whereIn('user_id', $managedOwnerIds)
            ->whereNotNull('first_login_at')
            ->sum('price') ?? 0;

        $totalManualIncome = \App\Models\Income::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->whereIn('user_id', $managedOwnerIds)
            ->sum('amount') ?? 0;
        
        // List of Owners with Detailed Stats
        $owners = User::whereIn('role', ['owner', 'mitra', 'mitra-reseller'])
            ->where('created_by', auth()->id())
            ->with(['mikrotikConfigs'])
            ->withSum(['billingHistories as total_vouchers_created' => function($query) {
                $query->withoutGlobalScope(\App\Scopes\TenantScope::class);
            }], 'price')
            ->withSum(['billingHistories as total_vouchers_sold' => function($query) {
                $query->withoutGlobalScope(\App\Scopes\TenantScope::class)
                      ->whereNotNull('first_login_at');
            }], 'price')
            ->withSum(['incomes as total_income_manual' => function($query) {
                $query->withoutGlobalScope(\App\Scopes\TenantScope::class);
            }], 'amount')
            ->get();

        return view('hotsupport.dashboard', compact(
            'totalOwners', 'totalRouters', 'owners',
            'totalVoucherCreated', 'totalVoucherSold', 'totalManualIncome'
        ));
    }

    public function impersonate($id)
    {
        $user = User::where('created_by', auth()->id())->findOrFail($id);
        
        // Store original ID
        session(['impersonated_by' => auth()->id()]);
        
        // Login as owner
        auth()->login($user);
        
        return redirect()->route('dashboard')->with('success', "Logged in as {$user->name}");
    }

    public function leaveImpersonation()
    {
        if (session()->has('impersonated_by')) {
            $originalId = session('impersonated_by');
            session()->forget('impersonated_by');
            
            auth()->loginUsingId($originalId);
            
            return redirect()->route('hotsupport.dashboard')->with('success', 'Welcome back, Admin.');
        }
        
        return redirect()->route('dashboard');
    }

    public function show($id)
    {
        $owner = User::with(['mikrotikConfigs'])
            ->where('created_by', auth()->id())
            ->findOrFail($id);
        
        $month = date('m');
        $year = date('Y');

        // Calculate Monthly Stats (Matches Billing Monitor)
        // Bypass TenantScope to see Owner's data
        
        $incomeVoucher = \App\Models\Income::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', 'Voucher')
            ->sum('amount');

        $incomeMember = \App\Models\Income::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', 'Member')
            ->sum('amount');
            
        $incomeReseller = \App\Models\Income::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', 'Reseller')
            ->sum('amount');

        // Additional PPPoE check if exists in DB (or map to Reseller/Member)
        // The dashboard requested PPPoE, but Monitor uses Reseller.
        // I will add PPPoE explicitly just in case.
        $incomePppoe = \App\Models\Income::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', 'PPPoE')
            ->sum('amount');

        $totalIncome = $incomeVoucher + $incomeMember + $incomeReseller + $incomePppoe;

        return view('hotsupport.owner.show', compact(
            'owner', 
            'totalIncome', 
            'incomeVoucher', 
            'incomeMember',
            'incomeReseller',
            'incomePppoe'
        ));
    }

    public function createRouter($id)
    {
        $owner = User::findOrFail($id);
        return view('hotsupport.router.create', compact('owner'));
    }

    public function storeRouter(Request $request, $id)
    {
        $request->validate([
            'host' => 'required',
            'user' => 'required',
            'pass' => 'required',
            'port' => 'required|numeric'
        ]);

        MikrotikConfig::create([
            'user_id' => $id,
            'host' => $request->host,
            'user' => $request->user,
            'pass' => $request->pass,
            'port' => $request->port
        ]);

        return redirect()->route('hotsupport.owner.show', $id)->with('success', 'Router added successfully');
    }

    public function editRouter($id)
    {
        $router = MikrotikConfig::findOrFail($id);
        return view('hotsupport.router.edit', compact('router'));
    }

    public function updateRouter(Request $request, $id)
    {
        $router = MikrotikConfig::findOrFail($id);
        
        $request->validate([
            'host' => 'required',
            'user' => 'required',
            'pass' => 'required',
            'port' => 'required|numeric'
        ]);

        $router->update([
            'host' => $request->host,
            'user' => $request->user,
            'pass' => $request->pass,
            'port' => $request->port
        ]);

        return redirect()->route('hotsupport.owner.show', $router->user_id)->with('success', 'Router updated successfully');
    }

    public function createOwner()
    {
        return view('hotsupport.owner.create');
    }

    public function storeOwner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|string|min:4',
            'location' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'notes' => 'nullable|string',
            'role' => 'required|in:mitra,mitra-reseller',
            'dns' => 'nullable|string',
            'winbox' => 'nullable|string',
            'ip_api' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'created_by' => auth()->id(), // Ownership tracking
            'location' => $request->location,
            'whatsapp' => $request->whatsapp,
            'notes' => $request->notes,
            'is_active' => true,
            'dns' => $request->dns,
            'winbox' => $request->winbox,
            'ip_api' => $request->ip_api,
        ]);

        return redirect()->route('hotsupport.dashboard')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function editOwner($id)
    {
        $owner = User::findOrFail($id);
        return view('hotsupport.owner.edit', compact('owner'));
    }

    public function updateOwner(Request $request, $id)
    {
        $owner = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $owner->id,
            'location' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'notes' => 'nullable|string',
            'password' => 'nullable|string|min:4',
            'role' => 'required|in:owner,mitra,mitra-reseller',
            'dns' => 'nullable|string',
            'winbox' => 'nullable|string',
            'ip_api' => 'nullable|string',
        ]);

        $owner->name = $request->name;
        $owner->username = $request->username;
        $owner->location = $request->location;
        $owner->whatsapp = $request->whatsapp;
        $owner->notes = $request->notes;
        $owner->role = $request->role;
        $owner->dns = $request->dns;
        $owner->winbox = $request->winbox;
        $owner->ip_api = $request->ip_api;

        if ($request->filled('password')) {
            $owner->password = bcrypt($request->password);
        }

        $owner->save();

        return redirect()->route('hotsupport.dashboard')->with('success', 'Data Mitra berhasil diperbarui.');
    }

    public function destroyRouter($id)
    {
        $router = \App\Models\MikrotikConfig::findOrFail($id);
        $userId = $router->user_id;
        $router->delete();
        
        return redirect()->route('hotsupport.owner.show', $userId)->with('success', 'Router deleted.');
    }

    public function reportForm()
    {
        return view('report');
    }

    public function sendReport(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = auth()->user();

        \App\Models\Report::create([
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'sender_name' => $user->name,
            'sender_role' => 'isp',
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending', 
        ]);

        return redirect()->route('hotsupport.tickets.index')->with('success', 'Ticket created successfully. We will reply shortly.');
    }

    public function ticketIndex()
    {
        $tickets = \App\Models\Report::where('sender_id', auth()->id())
            ->where('sender_type', get_class(auth()->user()))
            ->latest()
            ->get();
        return view('hotsupport.tickets.index', compact('tickets'));
    }

    public function ticketShow($id)
    {
        $ticket = \App\Models\Report::where('sender_id', auth()->id())
            ->where('sender_type', get_class(auth()->user()))
            ->findOrFail($id);
        
        if ($ticket->user_unread) {
            $ticket->update(['user_unread' => false]);
        }

        return view('hotsupport.tickets.show', compact('ticket'));
    }

    public function destroyOwner($id)
    {
        $user = User::whereIn('role', ['owner', 'mitra', 'mitra-reseller'])->where('created_by', auth()->id())->findOrFail($id);

        \DB::transaction(function () use ($user) {
            $userId = $user->id;

            // Delete all related data
            \App\Models\Income::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\BillingHistory::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\Expense::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\Debt::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\CustomerMember::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\Reseller::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\VoucherTemplate::withoutGlobalScopes()->where('user_id', $userId)->delete();
            \App\Models\Report::withoutGlobalScopes()
                ->where('sender_id', $userId)
                ->where('sender_type', \App\Models\User::class)
                ->delete();
            \App\Models\HotspotProfileMetadata::withoutGlobalScopes()->where('user_id', $userId)->delete();
            
            // Delete reseller accounts created by this owner
            User::where('created_by', $userId)->where('role', 'reseller')->delete();

            // Finally delete the user
            $user->delete();
        });

        return redirect()->route('hotsupport.dashboard')->with('success', 'Akun Mitra dan seluruh datanya telah berhasil dihapus secara permanen.');
    }

    // --- Mitra Reseller Management ---

    public function manageMitraResellerBalance()
    {
        $resellers = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->paginate(10);
        foreach ($resellers as $reseller) {
            $profile = \App\Models\Reseller::where('user_id', $reseller->id)->first();
            $reseller->balance = $profile ? $profile->balance : 0;
        }
        return view('hotsupport.mitra_reseller.balance', compact('resellers'));
    }

    public function addMitraResellerBalance(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required',
            'amount' => 'required|numeric|min:100',
        ]);
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($request->reseller_id);
        
        $profile = \App\Models\Reseller::where('user_id', $user->id)->first();
        if (!$profile) {
            $profile = \App\Models\Reseller::create(['user_id' => $user->id, 'name' => $user->name, 'balance' => 0]);
        }
        
        $before = $profile->balance;
        $profile->increment('balance', $request->amount);

        // Record History
        \App\Models\BalanceHistory::create([
            'user_id' => auth()->id(),
            'customer_id' => $user->id,
            'type' => 'IN',
            'amount' => $request->amount,
            'before_balance' => $before,
            'after_balance' => $before + $request->amount,
            'description' => 'Topup Saldo oleh ISP',
            'reference_id' => 'TOPUP-ISP-' . now()->format('YmdHis'),
        ]);
        
        return back()->with('success', 'Berhasil! Saldo ' . $user->name . ' ditambahkan sebesar Rp ' . number_format($request->amount, 0, ',', '.'));
    }

    public function resetMitraResellerBalance(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required',
        ]);
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($request->reseller_id);
        
        $profile = \App\Models\Reseller::where('user_id', $user->id)->first();
        if (!$profile) {
            $profile = \App\Models\Reseller::create(['user_id' => $user->id, 'name' => $user->name, 'balance' => 0]);
        }
        
        $before = $profile->balance;
        $profile->update(['balance' => 0]);

        // Record History
        \App\Models\BalanceHistory::create([
            'user_id' => auth()->id(),
            'customer_id' => $user->id,
            'type' => 'OUT',
            'amount' => $before,
            'before_balance' => $before,
            'after_balance' => 0,
            'description' => 'Reset Saldo oleh ISP',
            'reference_id' => 'RESET-ISP-' . now()->format('YmdHis'),
        ]);
        
        return back()->with('success', 'Berhasil! Saldo ' . $user->name . ' telah direset ke Rp 0.');
    }

    public function manageMitraResellerProfiles()
    {
        $resellers = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->paginate(10);
        return view('hotsupport.mitra_reseller.profiles_list', compact('resellers'));
    }

    public function viewMitraResellerProfiles($id)
    {
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        $profiles = [];
        
        if ($mkConfig) {
            try {
                $client = new \RouterOS\Client([
                    'host' => $mkConfig->host,
                    'user' => $mkConfig->user,
                    'pass' => $mkConfig->pass,
                    'port' => (int)($mkConfig->port ?? 8728),
                    'timeout' => 5,
                ]);
                $profiles = $client->query('/ip/hotspot/user/profile/print')->read();
                
                foreach ($profiles as &$prof) {
                    $meta = \App\Models\HotspotProfileMetadata::withoutGlobalScopes()
                                ->where('user_id', $user->id)
                                ->where('profile_name', $prof['name'])->first();
                    $prof['local_metadata'] = $meta;
                }
            } catch (\Exception $e) {
                // Ignore mikrotik error or flash it
                session()->flash('error', 'RouterOS Error: ' . $e->getMessage());
            }
        } else {
            session()->flash('error', 'Mitra (Reseller) ini belum memiliki konfigurasi Router.');
        }

        return view('hotsupport.mitra_reseller.profiles_show', compact('user', 'profiles'));
    }

    public function storeMitraResellerProfile(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'shared_users' => 'required|integer',
        ]);
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        
        if (!$mkConfig) return back()->with('error', 'Konfigurasi MikroTik tidak ditemukan untuk mitra ini.');
        
        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728)
            ]);
            $query = new \RouterOS\Query('/ip/hotspot/user/profile/add');
            $query->add('=name=' . $request->name);
            $query->add('=shared-users=' . $request->shared_users);
            
            if ($request->rate_limit) $query->add('=rate-limit=' . $request->rate_limit);
            if ($request->validity) $query->add('=session-timeout=' . $request->validity);
            
            try {
                $client->query($query)->read();
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'Undefined array key')) throw $e;
            }
            
            \App\Models\HotspotProfileMetadata::withoutGlobalScopes()->updateOrCreate(
                ['user_id' => $user->id, 'profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->sell_price ?? 0,
                    'validity' => $request->validity ?? '1d'
                ]
            );
            
            return back()->with('success', 'Profile berhasil ditambahkan untuk ' . $user->name);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan profile: ' . $e->getMessage());
        }
    }

    public function updateMitraResellerProfile(Request $request, $id) 
    {
        $request->validate([
            'mk_id' => 'required',
            'name' => 'required',
            'shared_users' => 'required|integer',
        ]);
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        
        if (!$mkConfig) return back()->with('error', 'Konfigurasi MikroTik tidak ditemukan.');
        
        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728)
            ]);
            $query = new \RouterOS\Query('/ip/hotspot/user/profile/set');
            $query->add('=.id=' . $request->mk_id);
            $query->add('=shared-users=' . $request->shared_users);
            
            if ($request->rate_limit) $query->add('=rate-limit=' . $request->rate_limit);
            if ($request->validity) $query->add('=session-timeout=' . $request->validity);
            
            try {
                $client->query($query)->read();
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'Undefined array key')) throw $e;
            }
            
            \App\Models\HotspotProfileMetadata::withoutGlobalScopes()->updateOrCreate(
                ['user_id' => $user->id, 'profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->sell_price ?? 0,
                    'validity' => $request->validity ?? '1d'
                ]
            );
            
            return back()->with('success', 'Profile berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate profile: ' . $e->getMessage());
        }
    }

    public function deleteMitraResellerProfile(Request $request, $id)
    {
        $mk_id = $request->query('mk_id');
        $name = $request->query('name');
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        
        if (!$mkConfig) return back()->with('error', 'Konfigurasi MikroTik tidak ditemukan.');
        
        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728)
            ]);
            $query = new \RouterOS\Query('/ip/hotspot/user/profile/remove');
            $query->add('=.id=' . $mk_id);
            
            try {
                $client->query($query)->read();
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'Undefined array key')) throw $e;
            }
            
            if ($name) {
                \App\Models\HotspotProfileMetadata::withoutGlobalScopes()
                    ->where('user_id', $user->id)
                    ->where('profile_name', $name)->delete();
            }
            
            return back()->with('success', 'Profile berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus profile: ' . $e->getMessage());
        }
    }

    public function mitraResellerBalanceHistory(Request $request)
    {
        $managedUserIds = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->pluck('id');
        
        $query = \App\Models\BalanceHistory::whereIn('customer_id', $managedUserIds)->with('customer');
        
        if ($request->filled('reseller_id')) {
            $query->where('customer_id', $request->reseller_id);
        }
        
        $history = $query->latest()->paginate(15);
        $resellers = User::whereIn('id', $managedUserIds)->get();
        $resellerMap = $resellers->keyBy('id');
        
        return view('hotsupport.mitra_reseller.balance_history', compact('history', 'resellers', 'resellerMap'));
    }

    public function downloadMitraResellerProfileTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_profile_mitra.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'shared_users', 'rate_limit', 'validity', 'price', 'sell_price']);
            fputcsv($file, ['1Jam-2000', '1', '1M/1M', '1h', '1000', '2000']);
            fputcsv($file, ['1Hari-5000', '1', '2M/2M', '1d', '3000', '5000']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importMitraResellerProfiles(Request $request, $id)
    {
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        if (!$mkConfig) {
            return back()->with('error', 'Mitra ini belum memiliki konfigurasi Router.');
        }

        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728),
                'timeout' => 10,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi Router Gagal: ' . $e->getMessage());
        }

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // skip header

        $successCount = 0;
        $errorCount = 0;

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($row) < 6) continue;

            $data = [
                'name' => $row[0],
                'shared_users' => $row[1],
                'rate_limit' => $row[2],
                'validity' => $row[3],
                'price' => $row[4],
                'sell_price' => $row[5],
            ];

            try {
                // 1. Add to MikroTik
                $query = new \RouterOS\Query('/ip/hotspot/user/profile/add');
                $query->add('=name=' . $data['name']);
                $query->add('=shared-users=' . $data['shared_users']);
                if ($data['rate_limit']) $query->add('=rate-limit=' . $data['rate_limit']);
                if ($data['validity']) $query->add('=session-timeout=' . $data['validity']);

                try {
                    $client->query($query)->read();
                } catch (\Exception $e) {
                    if (!str_contains($e->getMessage(), 'already exists')) {
                        throw $e;
                    }
                }

                // 2. Save Metadata (Bypass TenantScope for Mitra)
                \App\Models\HotspotProfileMetadata::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'profile_name' => $data['name']
                    ],
                    [
                        'price' => $data['price'],
                        'selling_price' => $data['sell_price'],
                        'validity' => $data['validity']
                    ]
                );
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        fclose($handle);
        return back()->with('success', "Import selesai. Berhasil: $successCount, Gagal: $errorCount.");
    }

    public function deleteBalanceHistory($id)
    {
        // Only allow if impersonating OR if the user is ISP/Builder
        if (!session('impersonated_by') && !in_array(auth()->user()->role, ['isp', 'builder'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus riwayat saldo.');
        }

        $history = \App\Models\BalanceHistory::findOrFail($id);
        $history->delete();

        return back()->with('success', 'Riwayat mutasi berhasil dihapus.');
    }

    // --- Mitra Reseller PPPoE Profile Management ---

    public function manageMitraResellerPppoeProfiles()
    {
        $resellers = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->paginate(10);
        return view('hotsupport.mitra_reseller.pppoe_profiles_list', compact('resellers'));
    }

    public function viewMitraResellerPppoeProfiles($id)
    {
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        $profiles = [];
        
        if ($mkConfig) {
            try {
                $client = new \RouterOS\Client([
                    'host' => $mkConfig->host,
                    'user' => $mkConfig->user,
                    'pass' => $mkConfig->pass,
                    'port' => (int)($mkConfig->port ?? 8728),
                    'timeout' => 5,
                ]);
                $profiles = $client->query('/ppp/profile/print')->read();
                
                foreach ($profiles as &$prof) {
                    $meta = \App\Models\PppoeProfileMetadata::withoutGlobalScopes()
                                ->where('user_id', $user->id)
                                ->where('profile_name', $prof['name'])->first();
                    $prof['local_metadata'] = $meta;
                }
            } catch (\Exception $e) {
                session()->flash('error', 'RouterOS Error: ' . $e->getMessage());
            }
        } else {
            session()->flash('error', 'Mitra (Reseller) ini belum memiliki konfigurasi Router.');
        }

        return view('hotsupport.mitra_reseller.pppoe_profiles_show', compact('user', 'profiles'));
    }

    public function storeMitraResellerPppoeProfile(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        
        if (!$mkConfig) return back()->with('error', 'Konfigurasi MikroTik tidak ditemukan.');
        
        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728)
            ]);
            $query = new \RouterOS\Query('/ppp/profile/add');
            $query->add('=name=' . $request->name);
            
            if ($request->local_address) $query->add('=local-address=' . $request->local_address);
            if ($request->remote_address) $query->add('=remote-address=' . $request->remote_address);
            if ($request->rate_limit) $query->add('=rate-limit=' . $request->rate_limit);
            if ($request->dns_server) $query->add('=dns-server=' . $request->dns_server);
            
            try {
                $client->query($query)->read();
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'Undefined array key')) throw $e;
            }
            
            \App\Models\PppoeProfileMetadata::withoutGlobalScopes()->updateOrCreate(
                ['user_id' => $user->id, 'profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->sell_price ?? 0
                ]
            );
            
            return back()->with('success', 'Profile PPPoE berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan profile: ' . $e->getMessage());
        }
    }

    public function updateMitraResellerPppoeProfile(Request $request, $id) 
    {
        $request->validate([
            'mk_id' => 'required',
            'name' => 'required',
        ]);
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        
        if (!$mkConfig) return back()->with('error', 'Konfigurasi MikroTik tidak ditemukan.');
        
        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728)
            ]);
            $query = new \RouterOS\Query('/ppp/profile/set');
            $query->add('=.id=' . $request->mk_id);
            if ($request->name) $query->add('=name=' . $request->name);
            
            if ($request->local_address) $query->add('=local-address=' . $request->local_address);
            if ($request->remote_address) $query->add('=remote-address=' . $request->remote_address);
            if ($request->rate_limit) $query->add('=rate-limit=' . $request->rate_limit);
            if ($request->dns_server) $query->add('=dns-server=' . $request->dns_server);
            
            try {
                $client->query($query)->read();
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'Undefined array key')) throw $e;
            }
            
            \App\Models\PppoeProfileMetadata::withoutGlobalScopes()->updateOrCreate(
                ['user_id' => $user->id, 'profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->sell_price ?? 0
                ]
            );
            
            return back()->with('success', 'Profile PPPoE berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate profile: ' . $e->getMessage());
        }
    }

    public function deleteMitraResellerPppoeProfile(Request $request, $id)
    {
        $mk_id = $request->query('mk_id');
        $name = $request->query('name');
        
        $user = User::where('role', 'mitra-reseller')->where('created_by', auth()->id())->findOrFail($id);
        $mkConfig = \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $user->id)->first();
        
        if (!$mkConfig) return back()->with('error', 'Konfigurasi MikroTik tidak ditemukan.');
        
        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728)
            ]);
            $query = new \RouterOS\Query('/ppp/profile/remove');
            $query->add('=.id=' . $mk_id);
            
            try {
                $client->query($query)->read();
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'Undefined array key')) throw $e;
            }
            
            if ($name) {
                \App\Models\PppoeProfileMetadata::withoutGlobalScopes()
                    ->where('user_id', $user->id)
                    ->where('profile_name', $name)->delete();
            }
            
            return back()->with('success', 'Profile PPPoE berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus profile: ' . $e->getMessage());
        }
    }
    public function paymentGatewayIndex()
    {
        $config = \App\Models\PaymentGatewayConfig::where('user_id', auth()->id())->first();
        return view('hotsupport.payment_gateway.index', compact('config'));
    }

    public function paymentGatewayUpdate(Request $request)
    {
        $request->validate([
            'merchant_code' => 'required|string',
            'api_key' => 'required|string',
            'mode' => 'required|in:sandbox,production',
        ]);

        \App\Models\PaymentGatewayConfig::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'provider' => 'pakasir',
                'merchant_code' => $request->merchant_code,
                'api_key' => $request->api_key,
                'mode' => $request->mode,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]
        );

        return redirect()->back()->with('success', 'Konfigurasi Pakasir berhasil disimpan.');
    }

    public function topupHistory()
    {
        $topups = \App\Models\TopupRequest::where('isp_id', auth()->id())
            ->with('user')
            ->latest()
            ->paginate(15);
            
        $config = \App\Models\PaymentGatewayConfig::where('user_id', auth()->id())->first();
            
        return view('hotsupport.payment_gateway.topup_history', compact('topups', 'config'));
    }

    public function simulateTopup($id)
    {
        $topup = \App\Models\TopupRequest::where('id', $id)
            ->where('isp_id', auth()->id())
            ->firstOrFail();

        $pakasir = new \App\Services\PakasirService(auth()->id());
        $response = $pakasir->simulatePayment($topup->merchant_ref, $topup->amount);

        if (isset($response['success']) && $response['success']) {
            return redirect()->back()->with('success', 'Simulasi pembayaran berhasil dikirim. Saldo akan segera diproses via webhook.');
        }

        return redirect()->back()->with('error', 'Gagal simulasi: ' . ($response['message'] ?? 'Unknown Error'));
    }
}
