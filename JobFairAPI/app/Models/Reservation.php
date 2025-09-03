<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'session_type',
        'session_time',
        'status',
        'notes',
        'user_message',
        'additional_info'
    ];

    protected $casts = [
        'session_time' => 'datetime',
        'additional_info' => 'array',
    ];

    /**
     * Get the user that owns the reservation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job that owns the reservation
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Scope for specific status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for specific session type
     */
    public function scopeBySessionType($query, $type)
    {
        return $query->where('session_type', $type);
    }

    /**
     * Scope for upcoming sessions
     */
    public function scopeUpcoming($query)
    {
        return $query->where('session_time', '>', now());
    }
}