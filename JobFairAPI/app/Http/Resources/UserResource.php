<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profession' => $this->profession,
            'experience_level' => $this->experience_level,
            'email_verified_at' => $this->email_verified_at,
            'upcoming_reservations_count' => $this->upcomingReservations()->count(),
            'total_reservations_count' => $this->reservations()->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
