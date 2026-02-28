<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MikrotikConfig;
use App\Models\Reseller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use RouterOS\Client;
use RouterOS\Config;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        $mkConfig = MikrotikConfig::where('user_id', auth()->id())->first();
        $routerTime = null;
        $routerResources = null;
        $routerStatus = $mkConfig ? 'Loading...' : 'Disconnected';
        $hotspotActiveCount = 0;
        $totalVoucherCount = 0;
        $pppoeUsersCount = 0;

        // 1. Connection & Time Sync (Moved to getRouterStatus for Asynchronous loading)
        // We only check if config exists to show 'Loading' state instead of blocking
        if ($mkConfig) {
            $cacheKey = 'router_stats_' . auth()->id();
            $cachedValues = Cache::get($cacheKey);
            
            if ($cachedValues) {
                $routerStatus = $cachedValues['routerStatus'];
                $routerTime = $cachedValues['routerTime'];
                $routerResources = $cachedValues['routerResources'];
                $hotspotActiveCount = $cachedValues['hotspotActiveCount'];
                $totalVoucherCount = $cachedValues['totalVoucherCount'];
                $pppoeUsersCount = $cachedValues['pppoeUsersCount'];
            }
        }

        // 2. Anchor Date for Stats
        $anchorDate = null;
        if ($routerTime) {
            try {
                $anchorDate = \Carbon\Carbon::createFromFormat('M/d/Y H:i:s', $routerTime['date'] . ' ' . $routerTime['time']);
            } catch (Exception $e) {}
        }
        $anchorDate = $anchorDate ?: now();

        // 3. Calculation Logic
        // Voucher Realtime (Total Generated - All Time)
        $voucherRealtime = DB::table('billing_history as bh')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->where('bh.user_id', auth()->id())
            ->whereNull('bh.first_login_at')
            ->sum(DB::raw('COALESCE(pm.price, bh.price)'));

        // Voucher Terjual (Monthly Sync with Anchor Date)
        $totalVoucherSold = DB::table('billing_history as bh')
            ->leftJoin('hotspot_profile_metadata as pm', function($join) {
                $join->on('bh.profile', '=', 'pm.profile_name')
                     ->on('bh.user_id', '=', 'pm.user_id');
            })
            ->where('bh.user_id', auth()->id())
            ->whereNotNull('bh.first_login_at')
            ->whereMonth('bh.first_login_at', $anchorDate->month)
            ->whereYear('bh.first_login_at', $anchorDate->year)
            ->sum(DB::raw('COALESCE(pm.price, bh.price)'));

        // Billing Manual (From Incomes table)
        $billingManual = DB::table('incomes')
            ->where('user_id', auth()->id())
            ->sum('amount');
        
        // Total Income
        $totalIncome = $totalVoucherSold + $billingManual;

        // 2. Resellers Count
        $totalResellers = Reseller::count();

        $resellerProfile = null;
        $recentTopups = [];
        if (auth()->user()->role === 'mitra-reseller') {
            $resellerProfile = \App\Models\Reseller::where('user_id', auth()->id())->first();
            $recentTopups = \App\Models\BalanceHistory::where('customer_id', auth()->id())
                ->where('type', 'IN')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'totalIncome', 
            'voucherRealtime',
            'totalVoucherSold',
            'billingManual',
            'totalResellers', 
            'hotspotActiveCount', 
            'totalVoucherCount', 
            'pppoeUsersCount', 
            'routerStatus',
            'routerResources',
            'routerTime',
            'resellerProfile',
            'recentTopups'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'dns' => 'nullable|string|max:255',
            'winbox' => 'nullable|string|max:255',
            'ip_api' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'location' => $request->location,
            'dns' => $request->dns,
            'winbox' => $request->winbox,
            'ip_api' => $request->ip_api,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:6',
            ]);
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
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
            'sender_role' => $user->role,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'unread',
        ]);

        $redirect = match($user->role) {
            'builder' => route('builder.dashboard'),
            'isp' => route('hotsupport.dashboard'),
            default => route('dashboard'),
        };

        return redirect($redirect)->with('success', 'Report sent to Mr. Koala successfully.');
    }

    public function reports()
    {
        $reports = \App\Models\Report::where('sender_id', auth()->id())
            ->where('sender_type', get_class(auth()->user()))
            ->latest()
            ->paginate(10);
        return view('report.index', compact('reports'));
    }

    public function viewReport($id)
    {
        $report = \App\Models\Report::where('sender_id', auth()->id())
            ->where('sender_type', get_class(auth()->user()))
            ->findOrFail($id);
        
        // Mark as read by user if admin replied
        if ($report->user_unread) {
            $report->update(['user_unread' => false]);
        }

        return view('report.show', compact('report'));
    }

    public function markBroadcastAsRead(Request $request, $id)
    {
        $broadcast = \App\Models\Broadcast::findOrFail($id);
        
        \DB::table('broadcast_reads')->insertOrIgnore([
            'broadcast_id' => $broadcast->id,
            'user_id' => auth()->id(),
            'read_at' => now(),
        ]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function getRouterStatus()
    {
        $mkConfig = MikrotikConfig::where('user_id', auth()->id())->first();
        if (!$mkConfig) {
            return response()->json(['routerStatus' => 'Disconnected']);
        }

        $cacheKey = 'router_stats_' . auth()->id();
        $cachedValues = Cache::remember($cacheKey, 120, function() use ($mkConfig) {
            try {
                $client = new Client([
                    'host' => $mkConfig->host,
                    'user' => $mkConfig->user,
                    'pass' => $mkConfig->pass,
                    'port' => (int)($mkConfig->port ?? 8728),
                    'timeout' => 5, // A bit longer for API
                ]);

                return [
                    'routerStatus' => 'Connected',
                    'routerTime' => $client->query('/system/clock/print')->read()[0] ?? null,
                    'routerResources' => $client->query('/system/resource/print')->read()[0] ?? null,
                    'hotspotActiveCount' => count($client->query('/ip/hotspot/active/print')->read()),
                    'totalVoucherCount' => count($client->query('/ip/hotspot/user/print')->read()),
                    'pppoeUsersCount' => count($client->query('/ppp/secret/print')->read()),
                ];
            } catch (Exception $e) {
                return [
                    'routerStatus' => 'Disconnected',
                    'routerTime' => null,
                    'routerResources' => null,
                    'hotspotActiveCount' => 0,
                    'totalVoucherCount' => 0,
                    'pppoeUsersCount' => 0,
                    'error' => $e->getMessage()
                ];
            }
        });

        return response()->json($cachedValues);
    }
}
