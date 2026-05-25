<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class KashierService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('payments.kashier');
    }

    protected function baseUrl(): string
    {
        return rtrim($this->config['base_url'] ?? 'https://test-api.kashier.io/v3', '/');
    }

    protected function headers(): array
    {
        return [
            'Authorization' => $this->config['secret_key'] ?? '',
            'api-key' => $this->config['api_key'] ?? '',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Build the payload according to Kashier Payment Sessions API.
     * Required fields: expireAt, maxFailureAttempts, paymentType, amount, currency, order (orderId),
     * merchantRedirect, display, type, merchantId, mode, allowedMethods, serverWebhook
     */
    public function buildPayload(Payment $payment, array $overrides = []): array
    {
        $amount = number_format((float) $payment->amount, 2, '.', '');
        $currency = $payment->currency;

        $order = 'order_payment_'.$payment->id;

        $payload = [
            // session expiry: 30 minutes from now
            'expireAt' => now()->addMinutes(30)->toIso8601String(),
            'maxFailureAttempts' => 3,
            'paymentType' => 'credit',
            'amount' => (string) $amount,
            'currency' => $currency,
            'order' => $order,
            'merchantId' => $this->config['merchant_id'] ?? null,
            'display' => 'en',
            'type' => 'one-time',
            'allowedMethods' => $overrides['allowedMethods'] ?? 'card,wallet',
            // Kashier's example payload and error message require metaData to be a JSON object,
            // despite what the text in their documentation says.
            'metaData' => (object) ($payment->metadata ?? []),
            'description' => $overrides['description'] ?? 'Unlock candidate access',
            'merchantRedirect' => $overrides['merchantRedirect'] ?? null,
            'serverWebhook' => $overrides['serverWebhook'] ?? null,
            'defaultMethod' => $overrides['defaultMethod'] ?? null,
        ];

        return array_merge($payload, $overrides);
    }

    public function createPaymentSession(Payment $payment, array $overrides = []): array
    {
        $url = $this->baseUrl().'/payment/sessions';

        $payload = $this->buildPayload($payment, $overrides);

        $resp = Http::withHeaders($this->headers())->post($url, $payload);

        if ($resp->failed()) {
            return ['success' => false, 'response' => $resp->body(), 'status' => $resp->status()];
        }

        $data = $resp->json();

        return ['success' => true, 'data' => $data, 'response' => $resp->body()];
    }

    /**
     * Get payment status using the documented endpoint: /payment/sessions/{sessionId}/payment
     */
    public function getPaymentStatus(string $sessionId): array
    {
        $url = $this->baseUrl()."/payment/sessions/{$sessionId}/payment";
        $resp = Http::withHeaders($this->headers())->get($url);

        if ($resp->failed()) {
            return ['success' => false, 'response' => $resp->body(), 'status' => $resp->status()];
        }

        return ['success' => true, 'data' => $resp->json(), 'response' => $resp->body()];
    }

    public function verifyPayment(Payment $payment): array
    {
        $sessionId = $payment->session_id ?? null;
        if (!$sessionId) {
            return ['success' => false, 'message' => 'No session id'];
        }

        return $this->getPaymentStatus($sessionId);
    }
}
