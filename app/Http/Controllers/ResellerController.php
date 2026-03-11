<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BillingHistory;
use App\Models\Income;
use App\Models\Reseller as ResellerModel;
use App\Models\VoucherTemplate;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\DB;
use RouterOS\Client;
use RouterOS\Query;
use Exception;
use App\Traits\VoucherTemplateHelpers;

class ResellerController extends Controller
{
    use VoucherTemplateHelpers;

    public function index()
    {
        $user = auth()->user();
        
        // Vouchers Sold
        $soldQuery = BillingHistory::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $user->created_by)
            ->where(function($q) use ($user) {
                $q->where('reseller_id', $user->id)
                  ->orWhere('notes', 'like', '%' . $user->username . '%');
            });

        $totalSoldCount = (clone $soldQuery)->count();
        $totalSoldPrice = (clone $soldQuery)->sum('price');
            
        // Current Balance from resellers table
        $resellerProf = ResellerModel::where('user_id', $user->id)->first();
        $balance = $resellerProf ? $resellerProf->balance : 0;

        // Vouchers Assigned / Distribution (All vouchers with this reseller_id)
        $totalVouchersCount = BillingHistory::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $user->created_by)
            ->where('reseller_id', $user->id)
            ->count();

        return view('reseller.dashboard', compact('totalSoldCount', 'totalSoldPrice', 'balance', 'totalVouchersCount'));
    }

    /**
     * Management for Owner to create Resellers
     */
    public function list()
    {
        $resellers = User::where('role', 'reseller')
            ->where('created_by', auth()->id())
            ->get();
            
        return view('owner.reseller.index', compact('resellers'));
    }

    public function create()
    {
        return view('owner.reseller.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|string|min:4',
            'location' => 'nullable|string',
            'whatsapp' => 'nullable|string',
        ]);

        $resellerUser = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'reseller',
            'created_by' => auth()->id(),
            'location' => $request->location,
            'origin' => 'hotpot',
            'is_active' => true,
        ]);

        // Create Reseller Profile
        ResellerModel::create([
            'user_id' => $resellerUser->id,
            'name' => $resellerUser->name,
            'balance' => 0,
            'phone' => $request->whatsapp,
        ]);

        return redirect()->route('owner.reseller.index')->with('success', 'Reseller berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $reseller = User::where('role', 'reseller')
            ->where('created_by', auth()->id())
            ->findOrFail($id);
            
        return view('owner.reseller.edit', compact('reseller'));
    }

    public function update(Request $request, $id)
    {
        $reseller = User::where('role', 'reseller')
            ->where('created_by', auth()->id())
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $reseller->id,
            'password' => 'nullable|string|min:4',
            'location' => 'nullable|string',
        ]);

        $reseller->name = $request->name;
        $reseller->username = $request->username;
        $reseller->location = $request->location;

        if ($request->filled('password')) {
            $reseller->password = bcrypt($request->password);
        }

        $reseller->save();

        return redirect()->route('owner.reseller.index')->with('success', 'Data Reseller berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $reseller = User::where('role', 'reseller')
            ->where('created_by', auth()->id())
            ->findOrFail($id);
            
        // Delete profiles from resellers table
        ResellerModel::where('user_id', $reseller->id)->delete();
        
        $reseller->delete();

        return redirect()->route('owner.reseller.index')->with('success', 'Reseller berhasil dihapus.');
    }

    /**
     * OWNER FEATURE: Manage balance for resellers (using CustomerMember table)
     */
    public function manageBalance()
    {
        $ownerId = auth()->id();
        
        if (auth()->user()->role === 'mitra-reseller') {
            $user = auth()->user();
            $profile = \App\Models\Reseller::where('user_id', $user->id)->first();
            if (!$profile) {
                $profile = \App\Models\Reseller::create(['user_id' => $user->id, 'name' => $user->name, 'balance' => 0]);
            }
            
            $lastDeposit = \App\Models\BalanceHistory::where('customer_id', $user->id)
                ->where('type', 'IN')
                ->latest()
                ->first();
                
            $totalTransactions = \App\Models\BalanceHistory::where('customer_id', $user->id)
                ->count();
                
            return view('reseller.balance', compact('profile', 'lastDeposit', 'totalTransactions'));
        }
        
        // All resellers for the dropdown
        $allResellers = \App\Models\CustomerMember::where('user_id', $ownerId)
            ->where('type', 'RESELLER')
            ->orderBy('name')
            ->get(['id', 'name', 'balance']);

        // Paginated resellers for the table
        $resellers = \App\Models\CustomerMember::where('user_id', $ownerId)
            ->where('type', 'RESELLER')
            ->orderBy('name')
            ->paginate(6);
            
        return view('owner.reseller.balance', [
            'resellers' => $resellers,
            'allResellers' => $allResellers
        ]);
    }

    public function myBalanceHistory()
    {
        $user = auth()->user();
        if ($user->role === 'mitra-reseller') {
            $history = \App\Models\BalanceHistory::where('customer_id', $user->id)
                ->latest()
                ->paginate(15);
                
            return view('reseller.balance_history', compact('history'));
        }
        
        return redirect()->back()->with('error', 'Akses ditolak.');
    }

    public function addBalance(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required',
            'amount' => 'required|numeric|min:100',
        ]);

        $reseller = \App\Models\CustomerMember::where('user_id', auth()->id())
            ->where('type', 'RESELLER')
            ->findOrFail($request->reseller_id);
            
        $before = $reseller->balance;
        $amount = (float)$request->amount;

        // Use Eloquent increment
        $reseller->increment('balance', $amount);

        // Record History
        \App\Models\BalanceHistory::create([
            'user_id' => auth()->id(),
            'customer_id' => $reseller->id,
            'type' => 'IN',
            'amount' => $amount,
            'before_balance' => $before,
            'after_balance' => $before + $amount,
            'description' => 'Topup Saldo oleh Owner',
            'reference_id' => 'TOPUP-' . now()->format('YmdHis'),
        ]);

        return back()->with('success', 'Berhasil! Saldo ' . $reseller->name . ' ditambahkan sebesar Rp ' . number_format($amount));
    }

    /**
     * RESELLER FEATURES
     */
    public function soldVouchers()
    {
        $user = auth()->user();
        $vouchers = BillingHistory::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $user->created_by)
            ->where(function($q) use ($user) {
                $q->where('reseller_id', $user->id)
                  ->orWhere('notes', 'like', '%' . $user->username . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reseller.vouchers', compact('vouchers'));
    }

    public function generateVoucher()
    {
        $user = auth()->user();
        $templates = $this->getVoucherTemplates($user->created_by);
        $profiles = [];
        $serverProfiles = [];
        
        $client = $this->getClient();
        if ($client) {
            try {
                $profiles = $client->query('/ip/hotspot/user/profile/print')->read();
                $serverProfiles = $client->query('/ip/hotspot/print')->read();

                // Attach local metadata (price, validity, etc.)
                $authId = auth()->id();
                foreach ($profiles as &$prof) {
                    $meta = \App\Models\HotspotProfileMetadata::withoutGlobalScope(\App\Scopes\TenantScope::class)
                        ->where('profile_name', $prof['name'])
                        ->where('user_id', $authId)
                        ->first();
                    $prof['local_metadata'] = $meta;
                }
                
                $profiles = array_values($profiles);
            } catch (Exception $e) {
                // Ignore error for now
            }
        }
        
        return view('reseller.generate', compact('templates', 'profiles', 'serverProfiles'));
    }

    private function getClient()
    {
        $user = auth()->user();
        // Use owner's config
        $mkConfig = MikrotikConfig::where('user_id', $user->created_by)->first();
        if (!$mkConfig) return null;

        try {
            $client = new Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728),
                'timeout' => 10,
            ]);
            
            return $client;
        } catch (Exception $e) {
            return null;
        }
    }

    public function balance()
    {
        $user = auth()->user();
        $profile = ResellerModel::where('user_id', $user->id)->first();
        if (!$profile) {
            $profile = ResellerModel::create(['user_id' => $user->id, 'name' => $user->name, 'balance' => 0]);
        }
        
        $lastDeposit = \App\Models\BalanceHistory::where('customer_id', $user->id)
            ->where('type', 'IN')
            ->latest()
            ->first();
            
        $totalTransactions = \App\Models\BalanceHistory::where('customer_id', $user->id)
            ->count();
            
        return view('reseller.balance', compact('profile', 'lastDeposit', 'totalTransactions'));
    }

    public function balanceHistory()
    {
        $user = auth()->user();
        $history = \App\Models\BalanceHistory::where('customer_id', $user->id)
            ->latest()
            ->paginate(15);
            
        return view('reseller.balance_history', compact('history'));
    }

    public function distribution()
    {
        $user = auth()->user();
        
        // Detailed distribution (batches generated for this reseller)
        $batches = DB::table('billing_history as bh')
            ->leftJoin('voucher_templates as vt', 'bh.template_id', '=', 'vt.id')
            ->select(
                'bh.batch_id',
                DB::raw('MAX(bh.generated_at) as generated_at'),
                DB::raw('MAX(bh.profile) as profile'),
                DB::raw('MAX(vt.name) as template_name'),
                DB::raw('COUNT(*) as qty'),
                DB::raw('SUM(bh.price) as total_price'),
                DB::raw('MAX(bh.payment_status) as payment_status')
            )
            ->where('bh.reseller_id', $user->id)
            ->whereNotNull('bh.batch_id')
            ->groupBy('bh.batch_id')
            ->orderBy('generated_at', 'desc')
            ->paginate(20);

        return view('reseller.distribution', compact('batches'));
    }
}
