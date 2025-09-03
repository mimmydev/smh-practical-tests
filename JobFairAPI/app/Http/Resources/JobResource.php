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
            'location' => $this->location,
            'skills_required' => $this->skills_required,
            'is_active' => $this->is_active,
            'available_slots' => $this->available_slots,
            'reserved_slots' => $this->confirmedReservations()->count(),
            'has_available_slots' => $this->hasAvailableSlots(),
            'exhibitor' => new ExhibitorResource($this->whenLoaded('exhibitor')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}