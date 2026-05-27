<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\EmployerCandidateUnlock;
use App\Services\KashierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class KashierController extends Controller
{
    protected $service;

    public function __construct(KashierService $service)
    {
        $this->service = $service;
    }

    /**
     * Webhook endpoint called by Kashier. We do not trust the payload — verify via API.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Kashier webhook received', $payload);

        // Try to extract session id from webhook payload then verify via Kashier API
        $sessionId = data_get($payload, 'data.sessionId') ?? data_get($payload, 'sessionId') ?? data_get($payload, '_id') ?? data_get($payload, 'id');
        if (!$sessionId) {
            return response()->json(['message' => 'No session id'], 400);
        }

        $statusResp = $this->service->getPaymentStatus($sessionId);
        if (!$statusResp['success']) {
            Log::warning('Failed to verify Kashier session', ['session' => $sessionId]);
            return response()->json(['message' => 'Verification failed'], 422);
        }
        $data = $statusResp['data'] ?? [];

        // response structure: { message: 'success', data: { sessionId, status, ... } }
        $apiData = data_get($data, 'data') ?? $data;

        // Find payment by session_id (we store Kashier _id/session identifier in payments.session_id)
        $payment = Payment::where('session_id', $sessionId)->first();
        if (!$payment) {
            Log::warning('Payment not found for session', ['session' => $sessionId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Store provider response
        $payment->provider_response = $apiData;

        $sessionStatus = data_get($apiData, 'status');

        if (is_string($sessionStatus) && in_array(strtolower($sessionStatus), ['paid', 'completed', 'successful'], true)) {
            $payment->status = Payment::STATUS_PAID;
            $payment->paid_at = now();
            $payment->provider_reference = data_get($apiData, 'sessionId') ?? data_get($apiData, 'orderId') ?? data_get($apiData, 'merchantOrderId');
            $payment->save();

            // Create unlock if not exists
            $this->createUnlockIfNeeded($payment);
            return response()->json(['message' => 'Payment verified and unlocked'], 200);
        }

        // mark failed or leave pending
        if (is_string($sessionStatus) && in_array(strtolower($sessionStatus), ['failed', 'declined'], true)) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
        } else {
            $payment->provider_response = $apiData;
            $payment->save();
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }

    protected function createUnlockIfNeeded(Payment $payment)
    {
        if (!$payment->application) {
            return;
        }

        $application = $payment->application;
        $employerId = $payment->employer_id;
        $candidateId = $application->candidate_id;

        $existing = EmployerCandidateUnlock::where('employer_id', $employerId)
            ->where('application_id', $application->id)
            ->exists();

        if ($existing) {
            return;
        }

        // Resolve a payment_reference from the transaction data stored on the payment.
        // Priority: provider_reference (Kashier session ID saved on success)
        //           → targetTransactionId / transactionId in provider_response
        //           → session_id as final fallback.
        $providerResponse = is_array($payment->provider_response)
            ? $payment->provider_response
            : (json_decode($payment->provider_response, true) ?? []);

        $paymentReference = $payment->provider_reference
            ?? data_get($providerResponse, 'targetTransactionId')
            ?? data_get($providerResponse, 'transactionId')
            ?? data_get($providerResponse, 'history.0.transactionId')
            ?? $payment->session_id
            ?? 'kashier-' . $payment->id;

        EmployerCandidateUnlock::create([
            'employer_id'       => $employerId,
            'candidate_id'      => $candidateId,
            'application_id'    => $application->id,
            'payment_id'        => $payment->id,
            'payment_reference' => $paymentReference,
            'unlocked_at'       => now(),
        ]);
    }

    /**
     * Kashier redirect success endpoint. Verify session and show a simple success response.
     */
    public function success(Request $request)
    {
        // Ensure the user is brought back to their local session.
        // Since Kashier rejects localhost URLs, they are redirected to ngrok.
        // We instantly bounce them back to localhost so their session cookies work.
        $appUrl = config('app.url');
        $requestHost = $request->getHost();
        $appHost = parse_url($appUrl, PHP_URL_HOST);

        if ($requestHost !== $appHost && in_array($appHost, ['localhost', '127.0.0.1'])) {
            $redirectUrl = rtrim($appUrl, '/') . $request->getRequestUri();
            return redirect()->away($redirectUrl);
        }

        $localPaymentId = $request->query('local_payment_id');
        $sessionId = $request->query('paymentId') ?? $request->query('sessionId') ?? $request->query('id') ?? $request->query('_id');
        
        $payment = null;
        if ($localPaymentId) {
            $payment = Payment::find($localPaymentId);
            if ($payment && !$sessionId) {
                $sessionId = $payment->session_id;
            }
        } elseif ($sessionId) {
            $payment = Payment::where('session_id', $sessionId)->first();
        }

        if (!$payment || !$sessionId) {
            Log::warning('[KashierController] Missing session id or payment record on success redirect.', ['query' => $request->query()]);
            return Inertia::render('Payments/Failure', ['error' => 'Could not identify payment record.']);
        }

        $statusResp = $this->service->getPaymentStatus($sessionId);
        if (!$statusResp['success']) {
            return Inertia::render('Payments/Failure', ['error' => 'Payment verification failed']);
        }

        $data = $statusResp['data'] ?? [];
        $apiData = data_get($data, 'data') ?? $data;

        $sessionStatus = data_get($apiData, 'status');
        if (is_string($sessionStatus) && in_array(strtolower($sessionStatus), ['paid', 'completed', 'successful'], true)) {
            // process as paid
            $payment->status = Payment::STATUS_PAID;
            $payment->paid_at = now();
            $payment->provider_response = $apiData;
            $payment->provider_reference = data_get($apiData, 'sessionId') ?? data_get($apiData, 'orderId') ?? data_get($apiData, 'merchantOrderId');
            $payment->save();

            $this->createUnlockIfNeeded($payment);

            return Inertia::render('Payments/Success', [
                'application_id' => $payment->application_id
            ]);
        }

        return Inertia::render('Payments/Failure', [
            'application_id' => $payment->application_id,
            'error' => 'Payment not completed.'
        ]);
    }

    public function failure(Request $request)
    {
        $appUrl = config('app.url');
        $requestHost = $request->getHost();
        $appHost = parse_url($appUrl, PHP_URL_HOST);

        if ($requestHost !== $appHost && in_array($appHost, ['localhost', '127.0.0.1'])) {
            $redirectUrl = rtrim($appUrl, '/') . $request->getRequestUri();
            return redirect()->away($redirectUrl);
        }

        $localPaymentId = $request->query('local_payment_id');
        $sessionId = $request->query('paymentId') ?? $request->query('sessionId') ?? $request->query('id') ?? $request->query('_id');
        $applicationId = null;
        
        $payment = null;
        if ($localPaymentId) {
            $payment = Payment::find($localPaymentId);
        } elseif ($sessionId) {
            $payment = Payment::where('session_id', $sessionId)->first();
        }

        if ($payment) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            $applicationId = $payment->application_id;
        }

        return Inertia::render('Payments/Failure', [
            'application_id' => $applicationId,
            'error' => 'Payment failed or canceled.'
        ]);
    }
}
