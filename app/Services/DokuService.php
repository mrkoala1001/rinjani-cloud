<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    protected $clientId;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = env('DOKU_CLIENT_ID');
        $this->secretKey = env('DOKU_SECRET_KEY');
        $this->baseUrl = env('DOKU_IS_PRODUCTION', false) 
            ? 'https://api.doku.com' 
            : 'https://api-sandbox.doku.com';
    }

    public function generateSignature($requestId, $timestamp, $targetPath, $body = null)
    {
        $digest = "";
        if ($body) {
            $digest = base64_encode(hash('sha256', json_encode($body), true));
        }

        $stringToSign = "Client-Id:" . $this->clientId . "\n" .
                        "Request-Id:" . $requestId . "\n" .
                        "Response-Timestamp:" . $timestamp . "\n" .
                        "Request-Target:" . $targetPath;
        
        if ($body) {
            $stringToSign .= "\nDigest:" . $digest;
        }

        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));

        return "HMACSHA256=" . $signature;
    }

    public function createPaymentLink($data)
    {
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $targetPath = '/checkout/v1/payment';

        $body = [
            'order' => [
                'amount' => $data['amount'],
                'invoice_number' => $data['invoice_number'],
                'currency' => 'IDR',
                'callback_url' => $data['callback_url'],
                'line_items' => $data['line_items'] ?? []
            ],
            'payment' => [
                'payment_due_date' => 60 // 60 minutes
            ],
            'customer' => [
                'id' => $data['customer_id'],
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? 'customer@example.com'
            ]
        ];

        $signature = $this->generatePaymentSignature($requestId, $timestamp, $targetPath, $body);

        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
        ])->post($this->baseUrl . $targetPath, $body);

        return $response->json();
    }

    protected function generatePaymentSignature($requestId, $timestamp, $targetPath, $body)
    {
        $digest = base64_encode(hash('sha256', json_encode($body), true));

        $stringToSign = "Client-Id:" . $this->clientId . "\n" .
                        "Request-Id:" . $requestId . "\n" .
                        "Request-Timestamp:" . $timestamp . "\n" .
                        "Request-Target:" . $targetPath . "\n" .
                        "Digest:" . $digest;

        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));

        return "HMACSHA256=" . $signature;
    }

    public function validateNotification($headers, $body)
    {
        $clientId = $headers['Client-Id'] ?? '';
        $requestId = $headers['Request-Id'] ?? '';
        $timestamp = $headers['Request-Timestamp'] ?? '';
        $signatureHeader = $headers['Signature'] ?? '';
        $targetPath = parse_url(request()->fullUrl(), PHP_URL_PATH);

        $digest = base64_encode(hash('sha256', json_encode($body), true));

        $stringToSign = "Client-Id:" . $clientId . "\n" .
                        "Request-Id:" . $requestId . "\n" .
                        "Request-Timestamp:" . $timestamp . "\n" .
                        "Request-Target:" . $targetPath . "\n" .
                        "Digest:" . $digest;

        $calculatedSignature = base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));
        $calculatedSignatureHeader = "HMACSHA256=" . $calculatedSignature;

        return hash_equals($calculatedSignatureHeader, $signatureHeader);
    }
}
