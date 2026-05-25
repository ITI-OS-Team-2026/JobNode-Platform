<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Payment;
use App\Services\KashierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create or return an existing pending payment for unlocking an application.
     *
     * Security rules enforced here:
     * - Amount is determined on the server via config('payments.unlock_candidate_price').
     * - Prevent duplicate pending payments for the same employer + application.
     * - Validate provider against allowed providers configured in `payments.allowed_providers`.
     * - Limit metadata to an array and sanitize values before storing.
     */
    public function store(Request $request, Application $application)
    {
        // Ensure the authenticated employer owns the job for this application
        if ($application->job->employer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'provider' => ['required', 'string'],
            'metadata' => ['sometimes', 'array'],
        ]);

        // Validate provider against allowed list from config
        $allowedProviders = config('payments.allowed_providers', ['kashier']);
        $provider = $request->input('provider');
        if (!in_array($provider, $allowedProviders, true)) {
            return response()->json(['message' => 'Invalid payment provider.'], 422);
        }

        $employerId = $request->user()->id;

        // Prevent duplicate pending payments for the same employer + application
        $existing = Payment::where('employer_id', $employerId)
            ->where('application_id', $application->id)
            ->where('status', Payment::STATUS_PENDING)
            ->first();

        if ($existing) {
            // ── Branch A: session_id exists → try to recover the sessionUrl ──────
            if ($existing->session_id) {
                Log::info('[PaymentController] Existing pending payment found with session_id.', [
                    'payment_id'     => $existing->id,
                    'application_id' => $application->id,
                    'session_id'     => $existing->session_id,
                ]);

                // Kashier's GET /payment/sessions/:id does NOT return the sessionUrl.
                // We must read it from the initial creation response we saved in the DB.
                $sessionUrl = data_get($existing->provider_response, 'sessionUrl')
                    ?? data_get($existing->provider_response, 'session.url');

                // Return existing:true only when we have a live sessionUrl
                if ($sessionUrl) {
                    return response()->json([
                        'id'         => $existing->id,
                        'status'     => $existing->status,
                        'amount'     => $existing->amount,
                        'currency'   => $existing->currency,
                        'existing'   => true,
                        'sessionUrl' => $sessionUrl,
                    ], 200);
                }
                
                // If we have a session_id but somehow lost the URL, fall through to createKashierSession.
                // Our new self-healing logic will catch the Kashier "already exists" error and recover the URL.
            }

            // ── Branch B: session_id is null → previous session creation failed ──
            //    Retry Kashier session creation using the EXISTING payment record.
            //    Do NOT return existing:true; the session was never established.
            Log::warning('[PaymentController] Existing pending payment has no session_id. Retrying Kashier session creation.', [
                'payment_id'     => $existing->id,
                'application_id' => $application->id,
                'employer_id'    => $employerId,
            ]);

            return $this->createKashierSession($existing, $application);
        }

        // Amount must come from server config only
        $amount = config('payments.unlock_candidate_price');
        if (is_null($amount)) {
            // Failsafe: require config to be set
            return response()->json(['message' => 'Unlock price not configured.'], 500);
        }

        // Currency comes from config or defaults to 'USD'
        $currency = config('payments.unlock_candidate_currency', 'USD');

        // Sanitize metadata: ensure array and convert non-scalar values to JSON strings
        $rawMetadata = $request->input('metadata', []);
        $metadata = [];
        if (is_array($rawMetadata)) {
            foreach ($rawMetadata as $k => $v) {
                $key = is_string($k) ? $k : (string)$k;
                if (is_null($v)) {
                    $metadata[$key] = null;
                } elseif (is_scalar($v)) {
                    $metadata[$key] = trim((string)$v);
                } else {
                    $metadata[$key] = json_encode($v);
                }
            }
        }

        $payment = Payment::create([
            'employer_id' => $employerId,
            'application_id' => $application->id,
            'amount' => $amount,
            'currency' => $currency,
            'provider' => $provider,
            'provider_reference' => null,
            'status' => Payment::STATUS_PENDING,
            'metadata' => $metadata,
        ]);

        // Create Kashier session and persist session_id + provider response
        if ($provider === 'kashier') {
            return $this->createKashierSession($payment, $application);
        }

        return response()->json([
            'id'       => $payment->id,
            'status'   => $payment->status,
            'amount'   => $payment->amount,
            'currency' => $payment->currency,
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Attempt to create a Kashier payment session for the given Payment record.
     *
     * Used both on first creation and on retry when session_id is null.
     * Logs every step so failures are visible in storage/logs/laravel.log.
     *
     * Returns:
     *   - redirect()->away($sessionUrl) on success
     *   - JSON 502 with Kashier's exact error on failure
     */
    private function createKashierSession(Payment $payment, Application $application)
    {
        /** @var KashierService $service */
        $service = app(KashierService::class);

        // Build public-facing URLs Kashier can actually reach.
        // config('payments.redirect_base_url') resolves to KASHIER_REDIRECT_BASE_URL
        // (ngrok in dev, real domain in prod) — never localhost.
        $baseUrl    = rtrim(config('payments.redirect_base_url'), '/');
        $returnUrl  = $baseUrl . '/payments/kashier/success?local_payment_id=' . $payment->id;
        $webhookUrl = $baseUrl . '/payments/kashier/webhook';

        $overrides = [
            'merchantRedirect' => $returnUrl,  // plain URL — Kashier validates as URL, not encoded
            'failureRedirect'  => false,
            'serverWebhook'    => $webhookUrl,
            'description'      => 'Unlock candidate access for application ' . $application->id,
            'order'            => 'order_payment_' . $payment->id,
            'customer'         => [
                'email'     => $payment->employer->email,
                'reference' => (string) $payment->employer->id,
            ],
        ];

        // ── Log the outgoing request ──────────────────────────────────────────
        Log::info('[PaymentController] Sending Kashier session creation request.', [
            'payment_id'     => $payment->id,
            'application_id' => $application->id,
            'amount'         => $payment->amount,
            'currency'       => $payment->currency,
            'overrides'      => $overrides,
        ]);

        $sessionResp = $service->createPaymentSession($payment, $overrides);

        // ── Log the raw response ──────────────────────────────────────────────
        Log::info('[PaymentController] Kashier session creation response.', [
            'payment_id' => $payment->id,
            'success'    => $sessionResp['success'],
            'http_status'=> $sessionResp['status'] ?? null,
            'body'       => $sessionResp['response'] ?? null,
        ]);

        if (! $sessionResp['success']) {
            // ── Log the failure with exact Kashier error ──────────────────────
            $rawBody  = $sessionResp['response'] ?? '';
            $httpCode = $sessionResp['status']   ?? null;
            $decoded  = json_decode($rawBody, true);
            $kashierMessage = data_get($decoded, 'message')
                ?? data_get($decoded, 'error.cause')
                ?? data_get($decoded, 'error')
                ?? data_get($decoded, 'errors.0')
                ?? $rawBody
                ?? 'Unknown Kashier error';

            // SELF-HEALING: If Kashier says the session already exists AND provides the sessionUrl,
            // we can safely recover and redirect the user.
            $recoveredUrl = data_get($decoded, 'sessionUrl');
            if ($recoveredUrl) {
                Log::info('[PaymentController] Recovered existing Kashier sessionUrl from error response.', [
                    'payment_id' => $payment->id,
                    'sessionUrl' => $recoveredUrl,
                ]);
                
                $payment->provider_response = $decoded;
                $payment->save();
                
                return response()->json([
                    'id'         => $payment->id,
                    'sessionUrl' => $recoveredUrl,
                    'existing'   => true,
                ], 200);
            }

            Log::error('[PaymentController] Kashier session creation FAILED.', [
                'payment_id'      => $payment->id,
                'application_id'  => $application->id,
                'http_status'     => $httpCode,
                'kashier_message' => $kashierMessage,
                'raw_body'        => $rawBody,
            ]);

            return response()->json([
                'message'         => 'Failed to create Kashier payment session.',
                'kashier_message' => is_string($kashierMessage) ? $kashierMessage : json_encode($kashierMessage),
                'http_status'     => $httpCode,
            ], 502);
        }

        // ── Session created successfully ──────────────────────────────────────
        $data = $sessionResp['data'] ?? [];

        $sessionId = data_get($data, '_id')
            ?? data_get($data, 'id')
            ?? data_get($data, 'session.id')
            ?? data_get($data, 'data.id');

        $sessionUrl = data_get($data, 'sessionUrl')
            ?? data_get($data, 'session.url')
            ?? data_get($data, 'data.sessionUrl');

        // Persist session_id and full provider response regardless of redirect
        $payment->session_id        = $sessionId;
        $payment->provider_response = $data;
        $payment->save();

        Log::info('[PaymentController] Kashier session saved.', [
            'payment_id' => $payment->id,
            'session_id' => $sessionId,
            'sessionUrl' => $sessionUrl,
        ]);

        if ($sessionUrl) {
            return response()->json([
                'id' => $payment->id,
                'sessionUrl' => $sessionUrl,
            ], 200);
        }

        // Session was created but no redirect URL returned — give the client what we have
        Log::warning('[PaymentController] Kashier session created but no sessionUrl in response.', [
            'payment_id' => $payment->id,
            'data'       => $data,
        ]);

        return response()->json([
            'id'       => $payment->id,
            'status'   => $payment->status,
            'amount'   => $payment->amount,
            'currency' => $payment->currency,
            'message'  => 'Session created but no redirect URL returned by Kashier.',
        ], 200);
    }
}
