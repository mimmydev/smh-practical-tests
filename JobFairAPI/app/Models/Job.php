<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhibitor_id',
        'title',
        'description',
        'requirements',
        'salary_range',
        'job_type',
        'location',
        'skills_required',
        'is_active',
        'available_slots'
    ];

    protected $casts = [
        'skills_required' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the exhibitor that owns the job
     */
    public function exhibitor(): BelongsTo
    {
        return $this->belongsTo(Exhibitor::class);
    }

    /**
     * Get the reservations for the job
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get confirmed reservations
     */
    public function confirmedReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)->where('status', 'confirmed');
    }

    /**
     * Scope for active jobs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific job type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('job_type', $type);
    }

    /**
     * Check if slots are available
     */
    public function hasAvailableSlots(): bool
    {
        $confirmedCount = $this->confirmedReservations()->count();
        return $confirmedCount < $this->available_slots;
    }
}