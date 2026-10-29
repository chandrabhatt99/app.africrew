<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'staffing_request_id',
        'user_id',
        'transaction_type',
        'merchant_request_id',
        'checkout_request_id',
        'mpesa_receipt_number',
        'phone_number',
        'amount',
        'account_reference',
        'transaction_desc',
        'status',
        'result_code',
        'result_desc',
        'transaction_date',
        'raw_callback_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'result_code' => 'integer',
        'transaction_date' => 'datetime',
        'raw_callback_payload' => 'array',
    ];

    public function staffingRequest(): BelongsTo
    {
        return $this->belongsTo(StaffingRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'completed' && $this->result_code === 0;
    }
}
