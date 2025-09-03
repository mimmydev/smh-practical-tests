<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExhibitorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'contact_email' => $this->when($this->shouldShowContactInfo($request), $this->contact_email),
            'phone' => $this->when($this->shouldShowContactInfo($request), $this->phone),
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'industry' => $this->industry,
            'address' => $this->address,
            'status' => $this->when($request->user()?->is_admin, $this->status),
            'booth_preferences' => $this->when($request->user()?->is_admin, $this->booth_preferences),
            'jobs_count' => $this->jobs()->count(),
            'active_jobs_count' => $this->activeJobs()->count(),
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Determine if contact info should be shown
     */
    private function shouldShowContactInfo(Request $request): bool
    {
        // Show contact info to admins or if user has a confirmed reservation
        return $request->user()?->is_admin || 
               $this->hasConfirmedReservationWithUser($request->user());
    }

    /**
     * Check if exhibitor has confirmed reservation with user
     */
    private function hasConfirmedReservationWithUser($user): bool
    {
        if (!$user) return false;
        
        return $this->jobs()->whereHas('reservations', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'confirmed');
        })->exists();
    }
}