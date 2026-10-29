<?php

namespace App\Services;

use App\Models\MpesaTransaction;
use App\Models\StaffingRequest;
use App\Services\EmailNotificationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected string $env;
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;
    protected string $passkey;
    protected string $callbackUrl;

    public function __construct()
    {
        $this->env = config('mpesa.env', 'sandbox');
        $this->baseUrl = $this->env === 'live' 
            ? config('mpesa.live_url', 'https://api.safaricom.co.ke') 
            : config('mpesa.sandbox_url', 'https://sandbox.safaricom.co.ke');
        
        $this->consumerKey = (string) config('mpesa.consumer_key', '');
        $this->consumerSecret = (string) config('mpesa.consumer_secret', '');
        $this->shortcode = (string) config('mpesa.shortcode', '174379');
        $this->passkey = (string) config('mpesa.passkey', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
        $this->callbackUrl = (string) config('mpesa.callback_url', url('/api/mpesa/stk-callback'));
    }

    /**
     * Get OAuth 2.0 access token from Safaricom Daraja API with caching.
     *
     * @throws Exception
     */
    public function getAccessToken(bool $forceRefresh = false): string
    {
        $cacheKey = "mpesa_access_token_{$this->env}";

        if (!$forceRefresh && Cache::has($cacheKey)) {
            return (string) Cache::get($cacheKey);
        }

        if (empty($this->consumerKey) || empty($this->consumerSecret)) {
            throw new Exception("M-Pesa Consumer Key or Consumer Secret is not configured in .env (MPESA_CONSUMER_KEY, MPESA_CONSUMER_SECRET).");
        }

        $url = "{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials";

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(15)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $expiresIn = (int) ($data['expires_in'] ?? 3599);

                if ($token) {
                    // Cache with safety margin
                    $ttl = max(60, $expiresIn - 120);
                    Cache::put($cacheKey, $token, now()->addSeconds($ttl));
                    return $token;
                }
            }

            Log::error('M-Pesa OAuth Generation Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new Exception("M-Pesa Authentication Failed: " . ($response->json('errorMessage') ?? $response->body()));
        } catch (Exception $e) {
            Log::error('M-Pesa getAccessToken Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Format a Kenyan phone number into Safaricom standard 254XXXXXXXXX format.
     */
    public function formatPhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Format 07... or 01... into 2547... / 2541...
        if (str_starts_with($clean, '0') && (strlen($clean) === 10)) {
            return '254' . substr($clean, 1);
        }

        // Format 7... or 1... into 2547... / 2541...
        if ((str_starts_with($clean, '7') || str_starts_with($clean, '1')) && (strlen($clean) === 9)) {
            return '254' . $clean;
        }

        // Already 254...
        if (str_starts_with($clean, '254') && strlen($clean) === 12) {
            return $clean;
        }

        return $clean;
    }

    /**
     * Initiate Lipa na M-Pesa Online (STK Push).
     *
     * @param string $phoneNumber (Customer phone)
     * @param float|int $amount (Transaction amount in KES)
     * @param string $accountReference (Reference e.g. Order #123)
     * @param string $transactionDesc (Description)
     * @param int|null $staffingRequestId (Optional linked booking ID)
     * @param int|null $userId (Optional authenticated user ID)
     * @return array
     */
    public function stkPush(
        string $phoneNumber,
        float|int $amount,
        string $accountReference = 'AfriCrew',
        string $transactionDesc = 'Escrow Payment',
        ?int $staffingRequestId = null,
        ?int $userId = null
    ): array {
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);

        if (strlen($formattedPhone) !== 12 || !str_starts_with($formattedPhone, '254')) {
            return [
                'success' => false,
                'message' => 'Invalid Safaricom phone number format. Please provide a valid Kenyan mobile number (e.g. 0712345678).',
            ];
        }

        if ($amount < 1) {
            return [
                'success' => false,
                'message' => 'Payment amount must be at least 1 KES.',
            ];
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $transactionType = config('mpesa.transaction_type', 'CustomerPayBillOnline');

        // If in sandbox mode and developer keys are not yet configured, provide seamless sandbox simulation
        if ($this->env === 'sandbox' && (empty($this->consumerKey) || empty($this->consumerSecret))) {
            $checkoutRequestId = 'ws_CO_SBX_' . date('YmdHis') . '_' . rand(1000, 9999);
            $merchantRequestId = 'SANDBOX-' . uniqid();

            $transaction = MpesaTransaction::create([
                'staffing_request_id' => $staffingRequestId,
                'user_id' => $userId,
                'transaction_type' => $transactionType,
                'merchant_request_id' => $merchantRequestId,
                'checkout_request_id' => $checkoutRequestId,
                'phone_number' => $formattedPhone,
                'amount' => $amount,
                'account_reference' => $accountReference,
                'transaction_desc' => $transactionDesc,
                'status' => 'pending',
            ]);

            Log::info('M-Pesa STK Push Sandbox Mode Initiated (Ready for PIN / Test Approval)', [
                'checkout_request_id' => $checkoutRequestId,
                'phone' => $formattedPhone,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
                'transaction_id' => $transaction->id,
                'customer_message' => 'M-Pesa STK push prompt sent to ' . $formattedPhone . '. Please enter PIN on phone or click Test Approval.',
            ];
        }

        try {
            $token = $this->getAccessToken();
            $url = "{$this->baseUrl}/mpesa/stkpush/v1/processrequest";

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => $transactionType,
                'Amount' => (int) round($amount), // Safaricom expects integer amounts in KES
                'PartyA' => $formattedPhone,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $formattedPhone,
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => substr($accountReference, 0, 12),
                'TransactionDesc' => substr($transactionDesc, 0, 13),
            ];

            Log::info('Initiating M-Pesa STK Push', ['payload' => array_merge($payload, ['Password' => '***'])]);

            $response = Http::withToken($token)
                ->timeout(20)
                ->post($url, $payload);

            $data = $response->json();

            Log::info('M-Pesa STK Push Response', ['status' => $response->status(), 'data' => $data]);

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                $checkoutRequestId = $data['CheckoutRequestID'] ?? null;
                $merchantRequestId = $data['MerchantRequestID'] ?? null;

                // Create initial pending transaction record in database
                $transaction = MpesaTransaction::create([
                    'staffing_request_id' => $staffingRequestId,
                    'user_id' => $userId,
                    'transaction_type' => $transactionType,
                    'merchant_request_id' => $merchantRequestId,
                    'checkout_request_id' => $checkoutRequestId,
                    'phone_number' => $formattedPhone,
                    'amount' => $amount,
                    'account_reference' => $accountReference,
                    'transaction_desc' => $transactionDesc,
                    'status' => 'pending',
                ]);

                return [
                    'success' => true,
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                    'transaction_id' => $transaction->id,
                    'customer_message' => $data['CustomerMessage'] ?? 'M-Pesa STK push prompt sent to your phone. Please enter your PIN.',
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? ($data['ResponseDescription'] ?? 'Failed to send M-Pesa prompt.'),
                'raw' => $data,
            ];
        } catch (Exception $e) {
            Log::error('M-Pesa STK Push Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Query status of an STK Push transaction.
     */
    public function queryStkPushStatus(string $checkoutRequestId): array
    {
        if ($this->env === 'sandbox' && (empty($this->consumerKey) || empty($this->consumerSecret))) {
            $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();
            return [
                'success' => true,
                'result_code' => $transaction?->result_code,
                'result_desc' => $transaction?->result_desc ?? 'Sandbox mode active',
                'transaction' => $transaction,
                'raw' => ['status' => $transaction?->status],
            ];
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        try {
            $token = $this->getAccessToken();
            $url = "{$this->baseUrl}/mpesa/stkpushquery/v1/query";

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ];

            $response = Http::withToken($token)
                ->timeout(15)
                ->post($url, $payload);

            $data = $response->json();

            // Find matching transaction
            $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

            if ($transaction && isset($data['ResultCode'])) {
                $resultCode = (int) $data['ResultCode'];
                $resultDesc = $data['ResultDesc'] ?? '';

                if ($resultCode === 0) {
                    $transaction->update([
                        'status' => 'completed',
                        'result_code' => $resultCode,
                        'result_desc' => $resultDesc,
                    ]);

                    // Automatically mark linked staffing request as paid if not already
                    if ($transaction->staffing_request_id) {
                        $this->markStaffingRequestPaid($transaction);
                    }
                } elseif (in_array($resultCode, [1032, 1037, 2001, 1])) {
                    // 1032 = cancelled by user, 1037 = timeout, 2001 = wrong PIN
                    $transaction->update([
                        'status' => $resultCode === 1032 ? 'cancelled' : 'failed',
                        'result_code' => $resultCode,
                        'result_desc' => $resultDesc,
                    ]);

                    if ($transaction->staffing_request_id) {
                        $staffingReq = StaffingRequest::find($transaction->staffing_request_id);
                        if ($staffingReq) {
                            EmailNotificationService::sendPaymentFailedToClient($staffingReq, $resultDesc);
                        }
                    }
                }
            }

            return [
                'success' => $response->successful(),
                'result_code' => $data['ResultCode'] ?? null,
                'result_desc' => $data['ResultDesc'] ?? null,
                'transaction' => $transaction,
                'raw' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Query STK Status Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process incoming Safaricom STK Push webhook callback.
     */
    public function handleStkCallback(array $payload): array
    {
        Log::info('Processing M-Pesa STK Callback Payload', ['payload' => $payload]);

        $stkCallback = $payload['Body']['stkCallback'] ?? null;

        if (!$stkCallback) {
            Log::warning('Invalid M-Pesa STK Callback Structure', ['payload' => $payload]);
            return ['status' => 'invalid_payload'];
        }

        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = (int) ($stkCallback['ResultCode'] ?? -1);
        $resultDesc = (string) ($stkCallback['ResultDesc'] ?? 'No description provided');

        // Locate corresponding transaction record
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            Log::warning('M-Pesa Transaction not found for CheckoutRequestID', [
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
            ]);

            // Create record if not found (e.g. initiated externally)
            $transaction = new MpesaTransaction([
                'merchant_request_id' => $merchantRequestId,
                'checkout_request_id' => $checkoutRequestId,
                'phone_number' => 'N/A',
                'amount' => 0,
            ]);
        }

        $transaction->result_code = $resultCode;
        $transaction->result_desc = $resultDesc;
        $transaction->raw_callback_payload = $payload;

        if ($resultCode === 0) {
            // SUCCESSFUL TRANSACTION
            $transaction->status = 'completed';

            // Extract items from CallbackMetadata
            $items = $stkCallback['CallbackMetadata']['Item'] ?? [];
            foreach ($items as $item) {
                $name = $item['Name'] ?? '';
                $value = $item['Value'] ?? null;

                if ($name === 'MpesaReceiptNumber' && $value) {
                    $transaction->mpesa_receipt_number = (string) $value;
                } elseif ($name === 'Amount' && $value) {
                    $transaction->amount = (float) $value;
                } elseif ($name === 'PhoneNumber' && $value) {
                    $transaction->phone_number = (string) $value;
                } elseif ($name === 'TransactionDate' && $value) {
                    try {
                        $transaction->transaction_date = Carbon::createFromFormat('YmdHis', (string) $value);
                    } catch (Exception) {
                        $transaction->transaction_date = now();
                    }
                }
            }

            $transaction->save();

            // Link & update Staffing Request if applicable
            if ($transaction->staffing_request_id) {
                $this->markStaffingRequestPaid($transaction);
            }

            Log::info('M-Pesa STK Transaction Successfully Completed', [
                'receipt' => $transaction->mpesa_receipt_number,
                'amount' => $transaction->amount,
                'phone' => $transaction->phone_number,
                'booking_id' => $transaction->staffing_request_id,
            ]);

            return ['status' => 'completed', 'receipt' => $transaction->mpesa_receipt_number];
        }

        // FAILED OR CANCELLED
        $transaction->status = ($resultCode === 1032) ? 'cancelled' : 'failed';
        $transaction->save();

        if ($transaction->staffing_request_id) {
            $staffingReq = StaffingRequest::find($transaction->staffing_request_id);
            if ($staffingReq) {
                EmailNotificationService::sendPaymentFailedToClient($staffingReq, $resultDesc);
            }
        }

        Log::info('M-Pesa STK Transaction Failed or Cancelled', [
            'result_code' => $resultCode,
            'result_desc' => $resultDesc,
            'checkout_request_id' => $checkoutRequestId,
        ]);

        return ['status' => $transaction->status, 'message' => $resultDesc];
    }

    /**
     * Mark staffing request as paid and allocate escrow to assigned professionals.
     */
    protected function markStaffingRequestPaid(MpesaTransaction $transaction): void
    {
        $staffingRequest = StaffingRequest::find($transaction->staffing_request_id);

        if (!$staffingRequest) {
            return;
        }

        DB::transaction(function () use ($staffingRequest, $transaction) {
            // Update request payment status
            $staffingRequest->update([
                'payment_status' => 'paid_to_admin',
                'payment_amount' => $transaction->amount,
                'payment_method' => 'mpesa',
                'paid_at' => $transaction->transaction_date ?: now(),
            ]);

            // Allocate escrow to assigned crew professionals
            $assignments = $staffingRequest->assignments()->with('professional')->get();
            if ($assignments->isNotEmpty()) {
                $assignedCount = $assignments->count();
                $perCrewPayout = round($transaction->amount / $assignedCount, 2);

                foreach ($assignments as $assignment) {
                    $prof = $assignment->professional;
                    if ($prof) {
                        $prof->increment('wallet_pending', $perCrewPayout);
                    }
                }
            }
        });

        // Send official payment receipt and payment confirmation email to client
        $invoiceNo = 'INV-' . ($transaction->mpesa_receipt_number ?: ('AFR' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT)));
        EmailNotificationService::sendReceiptToClient($staffingRequest, $transaction->amount, $invoiceNo);
        EmailNotificationService::sendPaymentConfirmedToClient($staffingRequest, $transaction->amount);

        // Notify assigned crew that client deposit is secured
        EmailNotificationService::sendDepositSecuredToAllCrew($staffingRequest);
    }

    /**
     * Register C2B URLs with Safaricom.
     */
    public function registerC2bUrls(string $validationUrl, string $confirmationUrl): array
    {
        $token = $this->getAccessToken();
        $url = "{$this->baseUrl}/mpesa/c2b/v1/registerurl";

        $payload = [
            'ShortCode' => $this->shortcode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl,
        ];

        $response = Http::withToken($token)
            ->timeout(15)
            ->post($url, $payload);

        return $response->json();
    }
}
