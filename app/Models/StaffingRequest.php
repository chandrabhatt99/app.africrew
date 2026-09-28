<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffingRequest extends Model
{
    protected $fillable = [
        'user_id', 'full_name', 'company_name', 'email', 'phone',
        'event_name', 'event_type', 'category', 'staff_count', 'event_date', 'dates',
        'start_time', 'end_time', 'shift_duration', 'location', 'requirements',
        'budget', 'rate_type', 'currency', 'attachment', 'status',
        'payment_status', 'payment_amount', 'payment_method', 'paid_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'dates' => 'array',
        'paid_at' => 'datetime',
        'payment_amount' => 'decimal:2',
        'budget' => 'decimal:2',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(StaffingAssignment::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
