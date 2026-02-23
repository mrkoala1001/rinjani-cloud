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
        $totalOwners = User::where('role', 'owner')->where('created_by', auth()->id())->count();
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
        $owners = User::where('role', 'owner')
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
            'password' => 'required|string|min:4',
            'location' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@hotsupport.local', // Fallback email
            'password' => bcrypt($request->password),
            'role' => 'owner',
            'created_by' => auth()->id(), // Ownership tracking
            'location' => $request->location,
            'whatsapp' => $request->whatsapp,
            'notes' => $request->notes,
            'is_active' => true,
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
        ]);

        $owner->name = $request->name;
        $owner->username = $request->username;
        $owner->location = $request->location;
        $owner->whatsapp = $request->whatsapp;
        $owner->notes = $request->notes;

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
        $user = User::where('role', 'owner')->where('created_by', auth()->id())->findOrFail($id);

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
}
