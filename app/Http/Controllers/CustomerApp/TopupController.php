<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerMember;
use App\Models\Topup;
use App\Models\BalanceHistory;
use App\Services\PakasirService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TopupController extends Controller
{
    protected $pakasir;

    public function __construct(PakasirService $pakasir)
    {
        $this->pakasir = $pakasir;
    }

    public function index()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        $pendingTopups = Topup::where('customer_id', $customerId)
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer_app.reseller.topup', compact('customer', 'pendingTopups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        $invoiceNumber = 'PV-' . now()->format('ymdHis') . strtoupper(Str::random(3));

        $data = [
            'amount' => (int) $request->amount,
            'invoice_number' => $invoiceNumber,
            'callback_url' => route('customer_app.reseller.topup.finish'),
        ];

        $paymentUrl = $this->pakasir->generatePaymentUrl($data);

        Topup::create([
            'user_id' => $customer->user_id,
            'customer_id' => $customer->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $request->amount,
            'status' => 'PENDING',
            'payment_url' => $paymentUrl
        ]);

        return redirect($paymentUrl);
    }

    public function finish(Request $request)
    {
        return redirect()->route('customer_app.reseller.topup')
            ->with('success', 'Transaksi sedang diproses. Silakan cek riwayat saldo Anda secara berkala.');
    }

    public function callback(Request $request)
    {
        // Pakasir Callback Handling
        // Example check if they send payment_status or similar
        Log::info('Pakasir Callback received:', $request->all());

        $invoiceNumber = $request->order_id;
        if (!$invoiceNumber) return response()->json(['status' => 'error', 'message' => 'No order_id'], 400);

        $topup = Topup::where('invoice_number', $invoiceNumber)->where('status', 'PENDING')->first();
        if (!$topup) return response()->json(['status' => 'error', 'message' => 'Topup not found or already processed'], 404);

        // Pakasir callback logic: if this URL is hit, it usually means success
        // or they send a specific status field. 
        $topup->update([
            'status' => 'SUCCESS',
            'reference_id' => $request->reference ?? 'PAKASIR-' . time()
        ]);

        $customer = CustomerMember::find($topup->customer_id);
        if ($customer) {
            $before = $customer->balance;
            $customer->increment('balance', $topup->amount);

            BalanceHistory::create([
                'user_id' => $customer->user_id,
                'customer_id' => $customer->id,
                'type' => 'IN',
                'amount' => $topup->amount,
                'before_balance' => $before,
                'after_balance' => $before + $topup->amount,
                'description' => 'Topup Mandiri (Pakasir): ' . $invoiceNumber,
                'reference_id' => $topup->invoice_number,
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
