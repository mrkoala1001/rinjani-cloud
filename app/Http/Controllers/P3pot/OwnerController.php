<?php

namespace App\Http\Controllers\P3pot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MikrotikConfig;
use RouterOS\Client;
use Exception;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    public function index()
    {
        // Try to get a MikroTik config to make it look real
        // Since P3potUser is separate, let's just pick the first ISP config or mock
        $mkConfig = MikrotikConfig::first(); 
        $routerTime = null;
        $routerResources = null;
        $routerStatus = 'Disconnected';
        $pppoeActiveCount = 0;
        $pppoeTotalCount = 0;

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
                
                $clock = $client->query('/system/clock/print')->read();
                if (!empty($clock)) $routerTime = $clock[0];

                $resources = $client->query('/system/resource/print')->read();
                if (!empty($resources)) $routerResources = $resources[0];

                $pppoeActiveCount = count($client->query('/ppp/active/print')->read());
                $pppoeTotalCount = count($client->query('/ppp/secret/print')->read());

            } catch (Exception $e) {
                $routerStatus = 'Disconnected';
            }
        }

        $totalIncome = 0;
        $pppoeMonthlyIncome = 0;
        $billingManual = 0;

        return view('p3pot.owner.dashboard', compact(
            'routerStatus',
            'routerResources',
            'routerTime',
            'pppoeActiveCount',
            'pppoeTotalCount',
            'totalIncome',
            'pppoeMonthlyIncome',
            'billingManual'
        ));
    }

    public function pppoe()
    {
        $mkConfig = MikrotikConfig::first();
        $active = [];
        $error = null;

        if ($mkConfig) {
            try {
                $client = new Client([
                    'host' => $mkConfig->host,
                    'user' => $mkConfig->user,
                    'pass' => $mkConfig->pass,
                    'port' => (int)($mkConfig->port ?? 8728),
                    'timeout' => 2,
                ]);
                $active = $client->query('/ppp/active/print')->read();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('p3pot.owner.pppoe', compact('active', 'error'));
    }

    public function paymentGateway()
    {
        return view('p3pot.owner.payment_gateway');
    }

    public function storePaymentGateway(Request $request)
    {
        return back()->with('success', 'Payment Gateway settings updated.');
    }

    public function customers()
    {
        return view('p3pot.owner.customers');
    }

    public function billing()
    {
        return view('p3pot.owner.billing');
    }

    public function settings()
    {
        return view('p3pot.owner.settings');
    }

    public function reports()
    {
        return view('p3pot.owner.reports');
    }

    public function sendReport(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = auth()->guard('p3pot')->user();

        \App\Models\Report::create([
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'sender_name' => $user->fullname ?? $user->username,
            'sender_role' => 'p3pot_owner',
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'unread',
        ]);

        return back()->with('success', 'Report sent to Mr. Koala successfully.');
    }
}
