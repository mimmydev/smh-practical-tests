<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'category' => $this->category,
            'category_display' => ucfirst($this->category),
            'status' => $this->status,
            'status_display' => $this->getStatusDisplay(),
            'admin_notes' => $this->when($request->user()?->is_admin, $this->admin_notes),
            'resolved_at' => $this->resolved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get human-readable status
     */
    private function getStatusDisplay(): string
    {
        return match($this->status) {
            'new' => 'New',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            default => ucfirst($this->status)
        };
    }
}
