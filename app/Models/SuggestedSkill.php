<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuggestedSkill extends Model
{
    protected $fillable = [
        'professional_id',
        'category',
        'skill_name',
        'status',
    ];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
