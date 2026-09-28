<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffingAssignment extends Model
{
    protected $fillable = [
        'staffing_request_id',
        'professional_id',
        'status',
        'notes',
    ];

    public function staffingRequest(): BelongsTo
    {
        return $this->belongsTo(StaffingRequest::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
