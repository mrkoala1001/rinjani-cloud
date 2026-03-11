<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Log;

class PakasirService
{
    protected $apiKey;
    protected $projectSlug;
    protected $baseUrl = 'https://app.pakasir.com/api/';

    public function __construct($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $config = PaymentGatewayConfig::where('user_id', $userId)->first();

        if ($config) {
            $this->apiKey = $config->api_key;
            $this->projectSlug = $config->merchant_code; // We use merchant_code field for project slug
        } else {
            // Fallback to env for backward compatibility or global config
            $this->apiKey = env('PAKASIR_API_KEY');
            $this->projectSlug = env('PAKASIR_PROJECT_SLUG');
        }
    }

    public function isConfigured()
    {
        return !empty($this->apiKey) && !empty($this->projectSlug);
    }

    /**
     * Create a transaction via Pakasir API
     */
    public function createTransaction($data)
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Pakasir not configured'];
        }

        $method = $data['method'] ?? 'qris';
        $url = $this->baseUrl . 'transactioncreate/' . $method;

        $payload = [
            'project'  => $this->projectSlug,
            'order_id' => $data['merchant_ref'],
            'amount'   => (int) $data['amount'],
            'api_key'  => $this->apiKey
        ];

        try {
            $response = Http::post($url, $payload);

            if ($response->successful()) {
                $res = $response->json();
                if (isset($res['payment'])) {
                    return [
                        'success' => true,
                        'data' => $res['payment']
                    ];
                }
            }

            return [
                'success' => false,
                'message' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Pakasir Error createTransaction: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get transaction detail from Pakasir
     */
    public function checkStatus($orderId, $amount)
    {
        $url = $this->baseUrl . 'transactiondetail';
        
        try {
            $response = Http::get($url, [
                'project'  => $this->projectSlug,
                'amount'   => (int) $amount,
                'order_id' => $orderId,
                'api_key'  => $this->apiKey
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Pakasir Error checkStatus: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Simulate payment for testing in Sandbox mode
     */
    public function simulatePayment($orderId, $amount)
    {
        $url = $this->baseUrl . 'paymentsimulation';

        $payload = [
            'project'  => $this->projectSlug,
            'order_id' => $orderId,
            'amount'   => (int) $amount,
            'api_key'  => $this->apiKey
        ];

        try {
            $response = Http::post($url, $payload);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Pakasir Error simulatePayment: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * List of available payment methods for Pakasir
     */
    public function getPaymentChannels()
    {
        return [
            ['code' => 'qris', 'name' => 'QRIS (All E-Wallet)'],
            ['code' => 'bri_va', 'name' => 'BRI Virtual Account'],
            ['code' => 'bni_va', 'name' => 'BNI Virtual Account'],
            ['code' => 'cimb_niaga_va', 'name' => 'CIMB Niaga VA'],
            ['code' => 'permata_va', 'name' => 'Permata Bank VA'],
            ['code' => 'atm_bersama_va', 'name' => 'ATM Bersama VA'],
            ['code' => 'bnc_va', 'name' => 'Neo Commerce VA'],
            ['code' => 'sampoerna_va', 'name' => 'Sahabat Sampoerna VA'],
            ['code' => 'maybank_va', 'name' => 'Maybank VA'],
            ['code' => 'artha_graha_va', 'name' => 'Artha Graha VA'],
            ['code' => 'paypal', 'name' => 'PayPal'],
        ];
    }
}
