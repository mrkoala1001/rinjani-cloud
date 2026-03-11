<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TopupRequest;
use App\Models\PaymentGatewayConfig;
use App\Models\Reseller;
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\Log;
use DB;

class TopupCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Pakasir Callback received:', $request->all());
        
        $data = $request->all();
        $orderId = $data['order_id'] ?? null;
        $amount = $data['amount'] ?? null;
        $status = $data['status'] ?? null;

        if (!$orderId || !$amount) {
            return response()->json(['success' => false, 'message' => 'Invalid data'], 400);
        }

        // Pakasir uses our merchant_ref as their order_id
        $topup = TopupRequest::where('merchant_ref', $orderId)->first();

        if (!$topup) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        if ($topup->status === 'PAID') {
            return response()->json(['success' => true, 'message' => 'Already processed']);
        }

        // Verify with Pakasir API for security
        $pakasir = new \App\Services\PakasirService($topup->isp_id);
        $verification = $pakasir->checkStatus($orderId, $amount);

        if (!$verification || !isset($verification['transaction'])) {
            return response()->json(['success' => false, 'message' => 'Verification failed'], 400);
        }

        $remoteStatus = $verification['transaction']['status'];

        if ($remoteStatus === 'completed') {
            DB::transaction(function () use ($topup) {
                $topup->update(['status' => 'PAID']);

                // Update Reseller Balance
                $reseller = Reseller::where('user_id', $topup->user_id)->first();
                if ($reseller) {
                    $before = $reseller->balance;
                    $reseller->increment('balance', $topup->amount);

                    // Record History
                    BalanceHistory::create([
                        'user_id' => $topup->isp_id,
                        'customer_id' => $topup->user_id,
                        'type' => 'IN',
                        'amount' => $topup->amount,
                        'before_balance' => $before,
                        'after_balance' => $before + $topup->amount,
                        'description' => 'Topup Otomatis via Pakasir (' . $topup->payment_method . ')',
                        'reference_id' => $topup->merchant_ref,
                    ]);
                }
            });

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Status is ' . $remoteStatus]);
    }
}
