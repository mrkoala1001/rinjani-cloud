<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\PlanHelper;
use App\Models\User;
use App\Models\PlanSubscription;
use App\Services\PakasirService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $plans = [
            'basic' => PlanHelper::getPlanConfig('basic'),
            'medium' => PlanHelper::getPlanConfig('medium'),
            'pro' => PlanHelper::getPlanConfig('pro'),
        ];
        
        return view('plan.index', compact('user', 'plans'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:medium,pro',
            'method' => 'required'
        ]);

        $user = auth()->user();
        $planCode = $request->plan;
        $planConfig = PlanHelper::getPlanConfig($planCode);
        
        // Convert price string to integer (e.g., "Rp 40.000 / Bulan" -> 40000)
        $amount = $planCode === 'medium' ? 40000 : 75000;

        $merchantRef = 'PLAN-' . strtoupper(Str::random(10));

        // Use global Pakasir config (no user_id passed to constructor)
        $pakasir = new PakasirService();
        
        $data = [
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'method' => $request->method,
        ];

        $payment = $pakasir->createTransaction($data);

        if ($payment['success']) {
            PlanSubscription::create([
                'user_id' => $user->id,
                'plan_code' => $planCode,
                'amount' => $amount,
                'payment_method' => $request->method,
                'merchant_ref' => $merchantRef,
                'payment_url' => $payment['data']['checkout_url'] ?? null,
                'status' => 'PENDING',
            ]);

            return response()->json([
                'success' => true,
                'checkout_url' => $payment['data']['checkout_url']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $payment['message'] ?? 'Gagal membuat transaksi'
        ], 400);
    }

    public function callback(Request $request)
    {
        $data = $request->all();
        $orderId = $data['order_id'] ?? null;
        $amount = $data['amount'] ?? null;

        if (!$orderId) return response()->json(['success' => false], 400);

        $subscription = PlanSubscription::where('merchant_ref', $orderId)->first();

        if (!$subscription) return response()->json(['success' => false], 404);
        if ($subscription->status === 'PAID') return response()->json(['success' => true]);

        // Verify with Pakasir
        $pakasir = new PakasirService();
        $verification = $pakasir->checkStatus($orderId, $amount);

        if ($verification && isset($verification['transaction']) && $verification['transaction']['status'] === 'completed') {
            DB::transaction(function () use ($subscription) {
                $subscription->update([
                    'status' => 'PAID',
                    'paid_at' => now()
                ]);

                $user = User::find($subscription->user_id);
                
                // Update User Plan
                $user->plan = $subscription->plan_code;
                
                // Calculate Expiry (add 30 days)
                $currentExpiry = $user->plan_expires_at && $user->plan_expires_at->isFuture() 
                    ? $user->plan_expires_at 
                    : now();
                
                $user->plan_expires_at = $currentExpiry->addDays(30);
                $user->save();
            });

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Status not completed'], 400);
    }
}
