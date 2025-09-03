<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exhibitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'contact_email',
        'phone',
        'website',
        'logo_url',
        'industry',
        'address',
        'status',
        'booth_preferences'
    ];

    protected $casts = [
        'booth_preferences' => 'array',
    ];

    /**
     * Get the jobs for the exhibitor
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get active jobs only
     */
    public function activeJobs(): HasMany
    {
        return $this->hasMany(Job::class)->where('is_active', true);
    }

    /**
     * Scope for approved exhibitors
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for specific industry
     */
    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }
}