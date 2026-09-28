<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'company_name', 'role', 'is_admin', 'permissions', 'google_id', 'facebook_id', 'avatar', 'auth_provider', 'api_token'])]
#[Hidden(['password', 'remember_token', 'api_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'permissions' => 'array',
        ];
    }

    /**
     * Determine if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin() && empty($this->permissions)) {
            return true; // Super admin has all permissions
        }
        return is_array($this->permissions) && in_array($permission, $this->permissions);
    }

    public function staffingRequests(): HasMany
    {
        return $this->hasMany(StaffingRequest::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Professional::class, 'client_favorites', 'user_id', 'professional_id')->withTimestamps();
    }
}
