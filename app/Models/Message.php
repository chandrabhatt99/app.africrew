<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'staffing_request_id',
        'proposal_id',
        'message',
        'attachment_path',
        'attachment_name',
        'is_read',
        'negotiated_price',
        'negotiation_status',
        'price_breakdown',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'negotiated_price' => 'decimal:2',
        'price_breakdown' => 'array',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function staffingRequest()
    {
        return $this->belongsTo(StaffingRequest::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}
