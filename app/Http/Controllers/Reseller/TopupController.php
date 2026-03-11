<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TopupRequest;
use App\Models\PaymentGatewayConfig;
use App\Services\PakasirService;
use Illuminate\Support\Str;

class TopupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!in_array($user->role, ['mitra-reseller', 'owner'])) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        // Get the ISP that created this reseller
        $ispId = $user->created_by;
        $pakasir = new PakasirService($ispId);
        
        if (!$pakasir->isConfigured()) {
            return redirect()->route('owner.reseller.balance')->with('error', 'Layanan Topup Otomatis belum dikonfigurasi oleh Admin.');
        }

        $channels = $pakasir->getPaymentChannels();
        return view('reseller.topup.index', compact('channels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'method' => 'required|string'
        ]);

        $user = auth()->user();
        $ispId = $user->created_by;
        $pakasir = new PakasirService($ispId);

        $merchantRef = 'TOPUP-' . now()->format('YmdHis') . Str::upper(Str::random(4));
        
        $data = [
            'method'         => $request->method,
            'merchant_ref'   => $merchantRef,
            'amount'         => $request->amount,
        ];

        $response = $pakasir->createTransaction($data);

        if ($response['success']) {
            $trx = $response['data'];
            
            TopupRequest::create([
                'user_id' => $user->id,
                'isp_id' => $ispId,
                'amount' => $request->amount,
                'fee' => $trx['fee'],
                'total' => $trx['total_payment'],
                'reference' => $trx['order_id'], // In Pakasir order_id is our merchant_ref
                'merchant_ref' => $merchantRef,
                'payment_method' => $request->method,
                'status' => 'UNPAID',
                'payment_detail' => json_encode($trx)
            ]);

            return redirect()->route('reseller.topup.show', $merchantRef);
        }

        return back()->with('error', 'Gagal membuat transaksi: ' . ($response['message'] ?? 'Unknown Error'));
    }

    public function show($reference)
    {
        $topup = TopupRequest::where('merchant_ref', $reference)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        $detail = json_decode($topup->payment_detail, true);
        
        return view('reseller.topup.show', compact('topup', 'detail'));
    }
}
