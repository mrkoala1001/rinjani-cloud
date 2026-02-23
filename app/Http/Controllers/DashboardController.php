<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MikrotikConfig;
use App\Models\Reseller;
use Illuminate\Support\Facades\DB;
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
        $routerStatus = 'Disconnected';
        $hotspotActiveCount = 0;
        $totalVoucherCount = 0;
        $pppoeUsersCount = 0;

        // 1. Connection & Time Sync
        if ($mkConfig) {
            try {
                $client = new Client([
                    'host' => $mkConfig->host,
                    'user' => $mkConfig->user,
                    'pass' => $mkConfig->pass,
                    'port' => (int)($mkConfig->port ?? 8728),
                    'timeout' => 2,
                ]);

                $routerStatus = 'Connected';
                
                // Fetch Time & Resources
                $clock = $client->query('/system/clock/print')->read();
                if (!empty($clock)) $routerTime = $clock[0];

                $resources = $client->query('/system/resource/print')->read();
                if (!empty($resources)) $routerResources = $resources[0];

                $hotspotActiveCount = count($client->query('/ip/hotspot/active/print')->read());
                $totalVoucherCount = count($client->query('/ip/hotspot/user/print')->read());
                $pppoeUsersCount = count($client->query('/ppp/secret/print')->read());

            } catch (Exception $e) {
                $routerStatus = 'Disconnected';
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
            ->leftJoin('hotspot_profile_metadata as pm', 'bh.profile', '=', 'pm.profile_name')
            ->where('bh.user_id', auth()->id())
            ->whereNull('bh.first_login_at')
            ->sum(DB::raw('COALESCE(pm.price, bh.price)'));

        // Voucher Terjual (Monthly Sync with Anchor Date)
        $totalVoucherSold = DB::table('billing_history as bh')
            ->leftJoin('hotspot_profile_metadata as pm', 'bh.profile', '=', 'pm.profile_name')
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
            'routerTime'
        ));
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
}
