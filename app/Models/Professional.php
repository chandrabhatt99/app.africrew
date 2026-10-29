<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professional extends Model
{
    protected $fillable = [
        'full_name', 'first_name', 'last_name', 'username', 'email', 'phone', 'password', 'profile_photo', 'cover_photo',
        'date_of_birth', 'gender', 'city', 'state', 'country',
        'languages', 'category', 'rating', 'custom_reviews_count', 'skills', 'featured_skills', 'expertises', 'services', 'education', 'show_education_on_cv', 'experience_records', 'experience_years', 'referees',
        'about', 'experience_summary', 'my_update', 'lets_connect', 'highlight_quote', 'hourly_rate', 'half_day_rate', 'full_day_rate',
        'one_day_rate', 'two_day_rate', 'rehearsal_rate', 'working_hours', 'rehearsal_option', 'has_multi_day_discount', 'multi_day_discount_pct', 'multi_day_min_days', 'multi_day_discount', 'rate_type', 'currency',
        'booking_policy', 'availability_dates', 'blocked_dates', 'assignment_types', 'gallery_photos',
        'availability', 'preferred_locations', 'other_cities', 'social_links', 'resume',
        'government_id', 'status', 'is_public', 'submitted_for_review_at', 'interview_date', 'interview_time', 'interview_link', 'interviewer_name', 'interview_notes', 'interview_status', 'payment_details',
        'email_verified_at', 'verification_token', 'is_onboarded',
        'wallet_pending', 'wallet_balance', 'wallet_withdrawn',
        'google_id', 'facebook_id', 'auth_provider', 'api_token'
    ];

    protected $hidden = ['password', 'api_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'submitted_for_review_at' => 'datetime',
        'interview_date' => 'date',
        'is_onboarded' => 'boolean',
        'show_education_on_cv' => 'boolean',
        'has_multi_day_discount' => 'boolean',
        'is_public' => 'boolean',
        'languages' => 'array',
        'preferred_locations' => 'array',
        'other_cities' => 'array',
        'social_links' => 'array',
        'gallery_photos' => 'array',
        'services' => 'array',
        'education' => 'array',
        'experience_records' => 'array',
        'referees' => 'array',
        'featured_skills' => 'array',
        'expertises' => 'array',
        'availability_dates' => 'array',
        'blocked_dates' => 'array',
        'assignment_types' => 'array',
        'payment_details' => 'array',
        'wallet_pending' => 'decimal:2',
        'wallet_balance' => 'decimal:2',
        'wallet_withdrawn' => 'decimal:2',
        'multi_day_discount_pct' => 'decimal:2',
    ];

    public function suggestedSkills(): HasMany
    {
        return $this->hasMany(SuggestedSkill::class);
    }

    public function getPipelineStatusAttribute(): string
    {
        if (is_null($this->email_verified_at)) {
            return 'Email Verification Pending';
        }
        if ($this->status === 'approved') {
            return 'Approved';
        }
        if ($this->status === 'rejected') {
            return 'Application Declined';
        }
        if ($this->status === 'suspended') {
            return 'Account Suspended';
        }
        if ($this->interview_status === 'scheduled' || !empty($this->interview_notes)) {
            return 'More Information Required / Interview Requested';
        }
        if (!is_null($this->submitted_for_review_at)) {
            return 'Under Review';
        }
        return 'Profile in Progress';
    }

    public function getCompletionPercentageAttribute(): int
    {
        $points = 0;
        $total = 6;

        // 1. Personal Information
        if (!empty($this->full_name) && (!empty($this->phone) || !empty($this->email))) {
            $points++;
        }
        // 2. About You (Work Bio)
        if (!empty($this->about) && strlen(trim($this->about)) >= 15) {
            $points++;
        }
        // 3. Experience & Qualifications
        if (!empty($this->experience_records) || !empty($this->education) || !empty($this->skills)) {
            $points++;
        }
        // 4. Portfolio
        if (!empty($this->profile_photo) || (!empty($this->gallery_photos) && count($this->gallery_photos) > 0)) {
            $points++;
        }
        // 5. Locations & Availability
        if (!empty($this->country) || !empty($this->city)) {
            $points++;
        }
        // 6. Service Rates
        if (!empty($this->full_day_rate) || !empty($this->hourly_rate) || !empty($this->one_day_rate) || !empty($this->half_day_rate)) {
            $points++;
        }

        return intval(($points / $total) * 100);
    }

    public function getNextIncompleteRequirementAttribute(): string
    {
        if (empty($this->full_name) || (empty($this->phone) && empty($this->email))) {
            return 'Personal Information (Name, Phone)';
        }
        if (empty($this->about) || strlen(trim($this->about)) < 15) {
            return 'About You (Work Bio & Description)';
        }
        if (empty($this->experience_records) && empty($this->education) && empty($this->skills)) {
            return 'Experience & Qualifications (Skills or Work History)';
        }
        if (empty($this->profile_photo) && (empty($this->gallery_photos) || count($this->gallery_photos) === 0)) {
            return 'Portfolio (Profile Photo or Portfolio Photos)';
        }
        if (empty($this->country) && empty($this->city)) {
            return 'Working Locations & Cities';
        }
        if (empty($this->full_day_rate) && empty($this->hourly_rate) && empty($this->one_day_rate) && empty($this->half_day_rate)) {
            return 'Service Rates & Daily Fee';
        }

        return 'Ready for Review!';
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function getRouteKey()
    {
        return !empty($this->username) ? $this->username : $this->id;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('username', $value)
            ->orWhere('id', $value)
            ->orWhere('full_name', $value)
            ->first();
    }

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
        $visibleCount = $this->visibleReviews()->count();
        if ($visibleCount > 0) {
            $avg = $this->visibleReviews()->avg('rating');
            return $avg !== null ? (float) round($avg, 1) : 0.0;
        }

        if ($this->reviews()->count() > 0) {
            $avg = $this->reviews()->avg('rating');
            return $avg !== null ? (float) round($avg, 1) : 0.0;
        }

        return 0.0;
    }

    public function getReviewsCountAttribute(): int
    {
        $visibleCount = $this->visibleReviews()->count();
        if ($visibleCount > 0) {
            return $visibleCount;
        }

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

    public function getVerifiedRefereesAttribute(): array
    {
        if (!is_array($this->referees)) {
            return [];
        }

        return array_values(array_filter($this->referees, function ($ref) {
            $item = is_string($ref) ? json_decode($ref, true) : $ref;
            if (!is_array($item)) return false;
            $status = strtolower($item['status'] ?? '');
            return ($status === 'verified' || !empty($item['is_confirmed']));
        }));
    }
}
