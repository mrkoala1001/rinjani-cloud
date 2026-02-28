<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CustomerMember;
use App\Models\BillingHistory;
use App\Models\User;
use App\Models\HotspotProfileMetadata;
use App\Traits\VoucherTemplateHelpers;

class DashboardController extends Controller
{
    use VoucherTemplateHelpers;

    public function showLogin()
    {
        return view('customer_app.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $customer = \App\Models\CustomerMember::where('app_username', $request->username)
            ->where('app_password', $request->password)
            ->first();

        if ($customer) {
            session(['customer_id' => $customer->id]);
            session(['customer_type' => $customer->type]);
            session(['customer_name' => $customer->name]);
            
            return redirect()->route('customer_app.dashboard');
        }

        return back()->with('error', 'Username atau Password aplikasi salah')->withInput();
    }

    public function index()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);

        if ($customer->type === 'RESELLER') {
            return $this->resellerDashboard($customer);
        } elseif ($customer->type === 'PERUMAHAN') {
            return $this->pppoeDashboard($customer);
        } else {
            return $this->memberDashboard($customer);
        }
    }

    protected function resellerDashboard($customer)
    {
        // Stats for current reseller
        $totalVouchers = BillingHistory::where('customer_id', $customer->id)->count();
        $totalSold = BillingHistory::where('customer_id', $customer->id)->whereNotNull('first_login_at')->count();
        
        // Revenue (sum of selling_price)
        $revenue = BillingHistory::where('customer_id', $customer->id)
            ->whereNotNull('first_login_at')
            ->sum('selling_price');

        return view('customer_app.dashboards.reseller', compact('customer', 'totalVouchers', 'totalSold', 'revenue'));
    }

    // --- VOUCHER FEATURES ---

    public function vouchers()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        
        $vouchers = BillingHistory::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get profiles from Mikrotik of the owner
        $owner = User::findOrFail($customer->user_id);
        $profiles = [];
        
        $mkConfig = $owner->mikrotikConfigs->first();
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
                
                // Attach metadata (price) - Scoped by owner
                foreach ($profiles as &$prof) {
                    $meta = HotspotProfileMetadata::where('user_id', $owner->id)
                        ->where('profile_name', $prof['name'])
                        ->first();
                    $prof['price'] = $meta ? ($meta->price ?? 0) : 0;
                }
            } catch (\Exception $e) {
                \Log::error("Reseller App Mikrotik Error: " . $e->getMessage());
            }
        }

        return view('customer_app.reseller.vouchers', compact('customer', 'vouchers', 'profiles'));
    }

    public function generateVoucher(Request $request)
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        
        $request->validate([
            'qty' => 'required|integer|min:1|max:100',
            'profile' => 'required',
            'timelimit' => 'nullable|string',
        ]);

        $profileName = $request->profile;
        $qty = (int)$request->qty;
        $timeLimit = $request->timelimit;

        // Check if profile exists and get price - Scoped by the owner (user_id)
        $meta = HotspotProfileMetadata::where('user_id', $customer->user_id)
            ->where('profile_name', $profileName)
            ->first();
            
        if (!$meta) return back()->with('error', 'Profile tidak ditemukan pada router owner.');

        $totalCost = $meta->price * $qty;

        if ($customer->balance < $totalCost) {
            return back()->with('error', 'Saldo tidak mencukupi. Saldo: Rp ' . number_format($customer->balance) . ', Dibutuhkan: Rp ' . number_format($totalCost));
        }

        $owner = User::find($customer->user_id);
        $mkConfig = $owner->mikrotikConfigs->first();
        if (!$mkConfig) return back()->with('error', 'Konfigurasi router owner belum tersedia.');

        try {
            $client = new \RouterOS\Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728),
                'timeout' => 15,
            ]);

            $vouchers = [];
            $batchId = 'APP-' . now()->format('YmdHis');

            $templates = $this->getVoucherTemplates($customer->user_id);
            $templateId = $templates->first() ? $templates->first()->id : null;

            for ($i = 0; $i < $qty; $i++) {
                $code = strtoupper(substr(md5(uniqid()), 0, 6));
                $pass = $code;

                $query = new \RouterOS\Query('/ip/hotspot/user/add');
                $query->add('=name=' . $code);
                $query->add('=password=' . $pass);
                $query->add('=profile=' . $profileName);
                $query->add('=comment=RESAPP: ' . $customer->name . ' (' . $customer->app_username . ')');

                if ($timeLimit) {
                    $query->add('=limit-uptime=' . $timeLimit);
                }

                $client->query($query)->read();

                $vouchers[] = [
                    'user_id' => $customer->user_id,
                    'customer_id' => $customer->id,
                    'reseller_id' => $customer->id, // Agar terbaca di menu distribusi owner
                    'voucher_code' => $code,
                    'username' => $code,
                    'password' => $pass,
                    'profile' => $profileName,
                    'price' => $meta->price,
                    'selling_price' => $meta->selling_price,
                    'timelimit' => $timeLimit ?: $meta->timelimit,
                    'validity' => $meta->validity,
                    'template_id' => $templateId,
                    'hotspotname' => $customer->name,
                    'batch_id' => $batchId,
                    'payment_status' => 'paid', // Status lunas karena potong saldo
                    'generated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            BillingHistory::insert($vouchers);
            
            $before = $customer->balance;

            // CRITICAL: Deduct Balance
            $customer->decrement('balance', $totalCost);

            // Record History
            \App\Models\BalanceHistory::create([
                'user_id' => $customer->user_id,
                'customer_id' => $customer->id,
                'type' => 'OUT',
                'amount' => $totalCost,
                'before_balance' => $before,
                'after_balance' => $before - $totalCost,
                'description' => 'Generate Voucher: ' . $qty . ' pcs (' . $profileName . ')',
                'reference_id' => $batchId,
            ]);

            return redirect()->route('customer_app.reseller.distribution')
                ->with('success', "Generate $qty Voucher Berhasil! Batch: $batchId");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function distribution()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);

        $batches = BillingHistory::where('customer_id', $customerId)
            ->select('batch_id', 'profile', DB::raw('count(*) as qty'), 'created_at')
            ->groupBy('batch_id', 'profile', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('customer_app.reseller.distribution', compact('customer', 'batches'));
    }

    public function viewBatch($batchId)
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        
        $vouchers = BillingHistory::where('customer_id', $customerId)
            ->where('batch_id', $batchId)
            ->get();
            
        return view('customer_app.reseller.batch_detail', compact('customer', 'batchId', 'vouchers'));
    }

    public function deleteBatch($batchId)
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        $owner = User::findOrFail($customer->user_id);
        
        $vouchers = BillingHistory::where('customer_id', $customerId)
            ->where('batch_id', $batchId)
            ->get();

        $mkConfig = $owner->mikrotikConfigs->first();
        if ($mkConfig) {
            try {
                $client = new \RouterOS\Client([
                    'host' => $mkConfig->host,
                    'user' => $mkConfig->user,
                    'pass' => $mkConfig->pass,
                    'port' => (int)($mkConfig->port ?? 8728),
                    'timeout' => 5,
                ]);

                foreach ($vouchers as $v) {
                    $mkUser = $client->query('/ip/hotspot/user/print', ['name' => $v->username])->read();
                    if (!empty($mkUser)) {
                        $client->query('/ip/hotspot/user/remove', ['.id' => $mkUser[0]['.id']])->read();
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Failed to delete vouchers from Mikrotik: " . $e->getMessage());
            }
        }

        BillingHistory::where('customer_id', $customerId)
            ->where('batch_id', $batchId)
            ->delete();

        return back()->with('success', 'Batch voucher berhasil dihapus.');
    }

    public function activeUsers()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        $owner = User::findOrFail($customer->user_id);

        $activeUsers = [];
        $mkConfig = $owner->mikrotikConfigs->first();
        if ($mkConfig) {
            try {
                $client = new \RouterOS\Client([
                    'host' => $mkConfig->host,
                    'user' => $mkConfig->user,
                    'pass' => $mkConfig->pass,
                    'port' => (int)($mkConfig->port ?? 8728),
                    'timeout' => 5,
                ]);
                $allActive = $client->query('/ip/hotspot/active/print')->read();
                
                // Filter by vouchers owned by this customer
                $myVoucherUsernames = BillingHistory::where('customer_id', $customerId)
                    ->pluck('username')
                    ->toArray();

                foreach ($allActive as $active) {
                    if (in_array($active['user'], $myVoucherUsernames)) {
                        $activeUsers[] = $active;
                    }
                }
            } catch (\Exception $e) {}
        }

        return view('customer_app.reseller.active_users', compact('customer', 'activeUsers'));
    }

    public function transactions()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);

        $transactions = BillingHistory::where('customer_id', $customerId)
            ->whereNotNull('first_login_at')
            ->orderBy('first_login_at', 'desc')
            ->paginate(20);

        return view('customer_app.reseller.transactions', compact('customer', 'transactions'));
    }

    public function profile()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        return view('customer_app.reseller.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        
        $request->validate([
            'name' => 'required',
            'app_password' => 'nullable|min:4',
        ]);

        $customer->name = $request->name;
        if ($request->filled('app_password')) {
            $customer->app_password = $request->app_password;
        }
        $customer->save();

        return back()->with('success', 'Profile diperbarui.');
    }

    public function balanceLogs()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        
        $pendingTopups = \App\Models\Topup::where('customer_id', $customerId)
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->get();

        $logs = \App\Models\BalanceHistory::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('customer_app.reseller.balance_logs', compact('customer', 'logs', 'pendingTopups'));
    }

    public function topup()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        return view('customer_app.reseller.topup', compact('customer'));
    }

    protected function pppoeDashboard($customer)
    {
        return view('customer_app.dashboards.pppoe', compact('customer'));
    }

    protected function memberDashboard($customer)
    {
        return view('customer_app.dashboards.member', compact('customer'));
    }

    public function logout()
    {
        session()->forget(['customer_id', 'customer_type', 'customer_name']);
        return redirect()->route('customer_app.login')->with('success', 'Berhasil logout.');
    }
}
