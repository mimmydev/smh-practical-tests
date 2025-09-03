<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'salary_range' => $this->salary_range,
            'job_type' => $this->job_type,
            'job_type_display' => $this->getJobTypeDisplay(),
            'location' => $this->location,
            'skills_required' => $this->skills_required,
            'is_active' => $this->is_active,
            'available_slots' => $this->available_slots,
            'reserved_slots' => $this->confirmedReservations()->count(),
            'remaining_slots' => $this->available_slots - $this->confirmedReservations()->count(),
            'has_available_slots' => $this->hasAvailableSlots(),
            'user_has_reservation' => $this->when($request->user() !== null, function() use ($request) {
                return $this->reservations()
                           ->where('user_id', $request->user()->id)
                           ->where('status', '!=', 'cancelled')
                           ->exists();
            }),
            'exhibitor' => new ExhibitorResource($this->whenLoaded('exhibitor')),
            'reservations' => ReservationResource::collection($this->whenLoaded('reservations')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get human-readable job type
     */
    private function getJobTypeDisplay(): string
    {
        return match($this->job_type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            default => ucfirst(str_replace('_', ' ', $this->job_type))
        };
    }
}
