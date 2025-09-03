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
            'contact_email' => $this->contact_email,
            'phone' => $this->phone,
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'industry' => $this->industry,
            'address' => $this->address,
            'status' => $this->status,
            'booth_preferences' => $this->booth_preferences,
            'jobs_count' => $this->jobs()->count(),
            'active_jobs_count' => $this->activeJobs()->count(),
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}