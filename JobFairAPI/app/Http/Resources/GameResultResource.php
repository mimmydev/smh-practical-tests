<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_email' => $this->when($request->user()?->is_admin, $this->user_email),
            'prize_won' => $this->prize_won,
            'prize_category' => $this->prize_category,
            'prize_category_display' => $this->getPrizeCategoryDisplay(),
            'session_id' => $this->session_id,
            'prize_claimed' => $this->prize_claimed,
            'claimed_at' => $this->claimed_at,
            'game_data' => $this->when($request->user()?->is_admin, $this->game_data),
            'won_at' => $this->created_at->format('M j, Y \a\t g:i A'),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Get human-readable prize category
     */
    private function getPrizeCategoryDisplay(): string
    {
        return match($this->prize_category) {
            'grand' => 'Grand Prize',
            'second' => 'Second Prize',
            'third' => 'Third Prize',
            'consolation' => 'Consolation Prize',
            default => ucfirst($this->prize_category) . ' Prize'
        };
    }
}
