<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'staffing_request_id',
        'user_id',
        'professional_id',
        'contract_number',
        'terms',
        'client_signed_at',
        'client_signature',
        'crew_signed_at',
        'crew_signature',
        'status',
    ];

    protected $casts = [
        'client_signed_at' => 'datetime',
        'crew_signed_at' => 'datetime',
    ];

    public function staffingRequest()
    {
        return $this->belongsTo(StaffingRequest::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
