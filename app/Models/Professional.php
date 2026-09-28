<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professional extends Model
{
    protected $fillable = [
        'full_name', 'first_name', 'last_name', 'username', 'email', 'phone', 'password', 'profile_photo', 'cover_photo',
        'date_of_birth', 'gender', 'city', 'state', 'country',
        'languages', 'category', 'skills', 'services', 'education', 'experience_records', 'experience_years',
        'about', 'highlight_quote', 'hourly_rate', 'half_day_rate', 'full_day_rate',
        'one_day_rate', 'two_day_rate', 'rehearsal_rate', 'multi_day_discount', 'rate_type', 'currency',
        'booking_policy', 'availability_dates', 'gallery_photos',
        'availability', 'preferred_locations', 'social_links', 'resume',
        'government_id', 'status', 'interview_date', 'interview_time', 'interviewer_name', 'interview_notes', 'interview_status', 'payment_details',
        'email_verified_at', 'verification_token', 'is_onboarded',
        'wallet_pending', 'wallet_balance', 'wallet_withdrawn',
        'google_id', 'facebook_id', 'auth_provider', 'api_token'
    ];

    protected $hidden = ['password', 'api_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'interview_date' => 'date',
        'is_onboarded' => 'boolean',
        'languages' => 'array',
        'preferred_locations' => 'array',
        'social_links' => 'array',
        'gallery_photos' => 'array',
        'services' => 'array',
        'education' => 'array',
        'experience_records' => 'array',
        'availability_dates' => 'array',
        'payment_details' => 'array',
        'wallet_pending' => 'decimal:2',
        'wallet_balance' => 'decimal:2',
        'wallet_withdrawn' => 'decimal:2',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(StaffingAssignment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function visibleReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_visible', true);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg !== null ? (float) round($avg, 1) : 0.0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getCompletedGigsCountAttribute(): int
    {
        $assignmentsCount = $this->assignments()->count();
        if ($assignmentsCount > 0) {
            return $assignmentsCount;
        }
        return $this->reviews()->count();
    }

    public function getOnTimeRateAttribute(): string
    {
        $totalAssignments = $this->assignments()->count();
        if ($totalAssignments > 0) {
            $onTime = $this->assignments()->whereIn('status', ['completed', 'assigned', 'accepted'])->count();
            return round(($onTime / $totalAssignments) * 100) . '%';
        }
        return '100%';
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'client_favorites', 'professional_id', 'user_id')->withTimestamps();
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return get_storage_url($this->profile_photo);
    }

    public function getCoverPhotoUrlAttribute(): ?string
    {
        return get_storage_url($this->cover_photo);
    }
}
