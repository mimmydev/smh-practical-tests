<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'session_type' => $this->session_type,
            'session_type_display' => $this->getSessionTypeDisplay(),
            'session_time' => $this->session_time,
            'session_time_formatted' => $this->session_time->format('M j, Y \a\t g:i A'),
            'session_date' => $this->session_time->format('Y-m-d'),
            'session_time_only' => $this->session_time->format('H:i'),
            'status' => $this->status,
            'status_display' => $this->getStatusDisplay(),
            'notes' => $this->when($this->shouldShowNotes($request), $this->notes),
            'user_message' => $this->user_message,
            'additional_info' => $this->additional_info,
            'is_upcoming' => $this->session_time->isFuture(),
            'is_past' => $this->session_time->isPast(),
            'is_today' => $this->session_time->isToday(),
            'can_cancel' => $this->canBeCancelled(),
            'time_until_session' => $this->session_time->diffForHumans(),
            'user' => new UserResource($this->whenLoaded('user')),
            'job' => new JobResource($this->whenLoaded('job')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get human-readable session type
     */
    private function getSessionTypeDisplay(): string
    {
        return match($this->session_type) {
            'job_matching' => 'Job Interview',
            'career_talk' => 'Career Talk Session',
            default => ucfirst(str_replace('_', ' ', $this->session_type))
        };
    }

    /**
     * Get human-readable status
     */
    private function getStatusDisplay(): string
    {
        return match($this->status) {
            'pending' => 'Pending Confirmation',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            default => ucfirst($this->status)
        };
    }

    /**
     * Check if reservation can be cancelled
     */
    private function canBeCancelled(): bool
    {
        return $this->session_time->isFuture() && 
               in_array($this->status, ['pending', 'confirmed']) &&
               $this->session_time->diffInHours(now()) > 2; // At least 2 hours notice
    }

    /**
     * Determine if notes should be shown
     */
    private function shouldShowNotes(Request $request): bool
    {
        // Show notes to the user who made the reservation or to admins/exhibitors
        return $request->user() && (
            $request->user()->id === $this->user_id ||
            $request->user()->is_admin ||
            $this->isExhibitorUser($request->user())
        );
    }

    /**
     * Check if user belongs to the exhibitor for this reservation
     */
    private function isExhibitorUser($user): bool
    {
        // This would require implementing exhibitor user relationships
        // For now, return false - implement when adding exhibitor user management
        return false;
    }
}
