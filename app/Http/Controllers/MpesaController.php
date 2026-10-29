<?php

namespace App\Http\Controllers;

use App\Models\MpesaTransaction;
use App\Models\StaffingRequest;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected MpesaService $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Initiate Lipa na M-Pesa Online (STK Push) from Client or Admin.
     */
    public function stkPush(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'staffing_request_id' => ['nullable', 'integer', 'exists:staffing_requests,id'],
            'account_reference' => ['nullable', 'string', 'max:12'],
            'transaction_desc' => ['nullable', 'string', 'max:20'],
        ]);

        $staffingRequestId = $validated['staffing_request_id'] ?? null;
        $accountReference = $validated['account_reference'] ?? ($staffingRequestId ? "Order #{$staffingRequestId}" : 'AfriCrew');
        $transactionDesc = $validated['transaction_desc'] ?? 'Escrow Payment';
        $userId = Auth::id();

        // If staffing_request_id is provided, verify client owns it or is admin
        if ($staffingRequestId) {
            $staffingRequest = StaffingRequest::find($staffingRequestId);
            if ($staffingRequest && !Auth::user()?->is_admin && $staffingRequest->user_id && $staffingRequest->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action on this booking request.',
                ], 403);
            }
        }

        $result = $this->mpesaService->stkPush(
            phoneNumber: $validated['phone'],
            amount: (float) $validated['amount'],
            accountReference: $accountReference,
            transactionDesc: $transactionDesc,
            staffingRequestId: $staffingRequestId,
            userId: $userId
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'checkout_request_id' => $result['checkout_request_id'],
                'message' => $result['customer_message'] ?? 'STK push prompt sent to your mobile phone.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send M-Pesa prompt. Please try again.',
        ], 422);
    }

    /**
     * Webhook receiver for Safaricom STK Push CallBack.
     */
    public function stkCallback(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Incoming Safaricom STK Push Webhook', ['payload' => $payload]);

        $this->mpesaService->handleStkCallback($payload);

        // Safaricom expects this standard confirmation response
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Callback received and processed successfully',
        ]);
    }

    /**
     * Check STK Push transaction status (used by front-end polling).
     */
    public function checkStatus(string $checkoutRequestId): JsonResponse
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Transaction not found.',
            ], 404);
        }

        // If still pending, query Safaricom directly to check real-time status
        if ($transaction->status === 'pending' && config('mpesa.consumer_key')) {
            $this->mpesaService->queryStkPushStatus($checkoutRequestId);
            $transaction->refresh();
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status, // pending, completed, failed, cancelled
            'is_paid' => $transaction->status === 'completed',
            'receipt' => $transaction->mpesa_receipt_number,
            'amount' => $transaction->amount,
            'phone' => $transaction->phone_number,
            'result_desc' => $transaction->result_desc,
        ]);
    }

    /**
     * Local / Sandbox simulation endpoint for testing without live public ngrok tunnel.
     */
    public function simulateSuccess(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'checkout_request_id' => ['required', 'string'],
            'mpesa_receipt' => ['nullable', 'string'],
        ]);

        $transaction = MpesaTransaction::where('checkout_request_id', $validated['checkout_request_id'])->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
        }

        $receipt = (!empty($validated['mpesa_receipt'])) 
            ? $validated['mpesa_receipt'] 
            : ('AFR' . strtoupper(substr(md5((string) microtime(true)), 0, 7)));

        $simulatedPayload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => $transaction->merchant_request_id ?: 'TEST-MERCHANT-1',
                    'CheckoutRequestID' => $transaction->checkout_request_id,
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully (Sandbox Simulation).',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => (float) $transaction->amount],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => $receipt],
                            ['Name' => 'TransactionDate', 'Value' => date('YmdHis')],
                            ['Name' => 'PhoneNumber', 'Value' => $transaction->phone_number],
                        ],
                    ],
                ],
            ],
        ];

        $this->mpesaService->handleStkCallback($simulatedPayload);

        return response()->json([
            'success' => true,
            'message' => 'M-Pesa payment simulated successfully.',
            'receipt' => $receipt,
        ]);
    }

    /**
     * Local / Sandbox simulation endpoint for testing payment failure.
     */
    public function simulateFailure(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'checkout_request_id' => ['required', 'string'],
            'reason' => ['nullable', 'string'],
        ]);

        $transaction = MpesaTransaction::where('checkout_request_id', $validated['checkout_request_id'])->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
        }

        $reason = $validated['reason'] ?? 'The user cancelled the transaction.';

        $simulatedPayload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => $transaction->merchant_request_id ?: 'TEST-MERCHANT-1',
                    'CheckoutRequestID' => $transaction->checkout_request_id,
                    'ResultCode' => 1032,
                    'ResultDesc' => $reason,
                ],
            ],
        ];

        $this->mpesaService->handleStkCallback($simulatedPayload);

        return response()->json([
            'success' => true,
            'message' => 'M-Pesa payment failure simulated successfully.',
        ]);
    }

    /**
     * C2B Validation Webhook.
     */
    public function c2bValidation(Request $request): JsonResponse
    {
        Log::info('C2B Validation Hook', ['payload' => $request->all()]);
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    /**
     * C2B Confirmation Webhook.
     */
    public function c2bConfirmation(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('C2B Confirmation Hook', ['payload' => $payload]);

        $transId = $payload['TransID'] ?? null;
        $amount = (float) ($payload['TransAmount'] ?? 0);
        $phone = (string) ($payload['MSISDN'] ?? '');
        $billRef = (string) ($payload['BillRefNumber'] ?? '');

        if ($transId) {
            MpesaTransaction::create([
                'transaction_type' => 'C2B',
                'mpesa_receipt_number' => $transId,
                'phone_number' => $phone,
                'amount' => $amount,
                'account_reference' => $billRef,
                'status' => 'completed',
                'result_code' => 0,
                'result_desc' => 'Direct C2B Paybill payment completed',
                'transaction_date' => now(),
                'raw_callback_payload' => $payload,
            ]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Confirmation received successfully']);
    }
}
