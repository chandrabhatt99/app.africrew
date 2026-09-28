<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'staffing_request_id',
        'professional_id',
        'custom_quote_amount',
        'travel_fee',
        'accommodation_fee',
        'notes',
        'status',
        'counter_amount',
    ];

    protected $casts = [
        'custom_quote_amount' => 'decimal:2',
        'travel_fee' => 'decimal:2',
        'accommodation_fee' => 'decimal:2',
        'counter_amount' => 'decimal:2',
    ];

    public function staffingRequest()
    {
        return $this->belongsTo(StaffingRequest::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
