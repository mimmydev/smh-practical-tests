<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profession',
        'experience_level',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the reservations for the user
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get upcoming reservations
     */
    public function upcomingReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)
                    ->where('session_time', '>', now())
                    ->where('status', 'confirmed')
                    ->orderBy('session_time', 'asc');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin ?? false;
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Get user's experience level display name
     */
    public function getExperienceLevelDisplayAttribute(): string
    {
        return match($this->experience_level) {
            'entry' => 'Entry Level',
            'junior' => 'Junior Level',
            'mid' => 'Mid Level',
            'senior' => 'Senior Level', 
            'lead' => 'Lead/Principal',
            'executive' => 'Executive',
            default => 'Not Specified'
        };
    }
}