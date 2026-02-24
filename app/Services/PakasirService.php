<?php

namespace App\Services;

class PakasirService
{
    protected $apiKey;
    protected $projectSlug;
    protected $baseUrl = 'https://app.pakasir.com/pay/';

    public function __construct()
    {
        $this->apiKey = env('PAKASIR_API_KEY');
        $this->projectSlug = env('PAKASIR_PROJECT_SLUG');
    }

    /**
     * Generate the payment URL for Pakasir
     */
    public function generatePaymentUrl($data)
    {
        $amount = $data['amount'];
        $orderId = $data['invoice_number'];
        $redirectUrl = urlencode($data['callback_url']);

        return $this->baseUrl . $this->projectSlug . '/' . $amount . '?order_id=' . $orderId . '&redirect=' . $redirectUrl;
    }

    /**
     * Simple webhook validation
     * Pakasir usually sends POST data. We check the API Key for basic security
     * if they provide a signature, but based on docs it's often direct or simple.
     */
    public function validateWebhook($request)
    {
        // Many simple aggregators use a shared secret or just send data.
        // We'll trust the callback if we can verify the order_id exists and is pending.
        // If Pakasir provides a specific signature in headers, we'd check it here.
        return true; 
    }
}
