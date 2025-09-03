<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * Get available prizes configuration
     */
    public function prizes(): JsonResponse
    {
        try {
            // This could be moved to database or config file
            $prizes = [
                [
                    'id' => 1,
                    'name' => 'Grand Prize - Laptop',
                    'category' => 'grand',
                    'quantity' => 1,
                    'probability' => 5, // 5% chance
                    'color' => '#FFD700',
                    'icon' => '💻'
                ],
                [
                    'id' => 2,
                    'name' => 'Second Prize - Smartphone',
                    'category' => 'second',
                    'quantity' => 3,
                    'probability' => 15, // 15% chance
                    'color' => '#C0C0C0',
                    'icon' => '📱'
                ],
                [
                    'id' => 3,
                    'name' => 'Bluetooth Headphones',
                    'category' => 'third',
                    'quantity' => 10,
                    'probability' => 25, // 25% chance
                    'color' => '#CD7F32',
                    'icon' => '🎧'
                ],
                [
                    'id' => 4,
                    'name' => 'Gift Voucher $50',
                    'category' => 'consolation',
                    'quantity' => 50,
                    'probability' => 35, // 35% chance
                    'color' => '#4CAF50',
                    'icon' => '🎁'
                ],
                [
                    'id' => 5,
                    'name' => 'Coffee Mug',
                    'category' => 'consolation',
                    'quantity' => 100,
                    'probability' => 20, // 20% chance
                    'color' => '#2196F3',
                    'icon' => '☕'
                ]
            ];
            
            // Check remaining quantities from database
            foreach ($prizes as &$prize) {
                $won = GameResult::where('prize_won', $prize['name'])->count();
                $prize['remaining'] = max(0, $prize['quantity'] - $won);
                $prize['is_available'] = $prize['remaining'] > 0;
            }
            
            return response()->json([
                'data' => array_filter($prizes, fn($p) => $p['is_available'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch prizes', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to load game prizes.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
    
    /**
     * Spin the wheel and determine winner
     */
    public function spin(Request $request): JsonResponse
    {
        $request->validate([
            'user_email' => 'required|email:rfc,dns',
        ]);
        
        try {
            $userEmail = $request->user_email;
            $sessionId = Str::uuid();
            
            // Check if user has already played (optional rate limiting)
            $recentPlay = GameResult::where('user_email', $userEmail)
                                   ->where('created_at', '>', now()->subHours(24))
                                   ->first();
            
            if ($recentPlay) {
                return response()->json([
                    'message' => 'You can only play once per day. Come back tomorrow!',
                    'error' => 'RATE_LIMITED',
                    'last_prize' => $recentPlay->prize_won
                ], 429);
            }
            
            // Get available prizes
            $prizesResponse = $this->prizes();
            $prizes = $prizesResponse->getData()->data;
            
            if (empty($prizes)) {
                return response()->json([
                    'message' => 'Sorry, all prizes have been won! Thanks for playing.',
                    'error' => 'NO_PRIZES_AVAILABLE'
                ], 400);
            }
            
            // Determine winner based on probabilities
            $winner = $this->determineWinner($prizes);
            
            if (!$winner) {
                return response()->json([
                    'message' => 'Better luck next time!',
                    'error' => 'NO_WIN'
                ], 200);
            }
            
            // Record the result
            $gameResult = GameResult::create([
                'user_email' => $userEmail,
                'prize_won' => $winner['name'],
                'prize_category' => $winner['category'],
                'session_id' => $sessionId,
                'game_data' => [
                    'wheel_position' => rand(0, 359),
                    'spin_duration' => rand(2000, 4000),
                    'total_prizes' => count($prizes)
                ]
            ]);
            
            Log::info('Game played - Winner!', [
                'user_email' => $userEmail,
                'prize' => $winner['name'],
                'session_id' => $sessionId
            ]);
            
            return response()->json([
                'message' => 'Congratulations! You won!',
                'winner_index' => array_search($winner, $prizes),
                'prize' => $winner,
                'session_id' => $sessionId,
                'game_data' => $gameResult->game_data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Game spin failed', [
                'user_email' => $request->user_email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Game error occurred. Please try again.',
                'error' => 'GAME_FAILED'
            ], 500);
        }
    }
    
    /**
     * Determine winner based on probability
     */
    private function determineWinner(array $prizes): ?array
    {
        $totalProbability = array_sum(array_column($prizes, 'probability'));
        $random = rand(1, $totalProbability);
        
        $cumulativeProbability = 0;
        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize['probability'];
            if ($random <= $cumulativeProbability) {
                return $prize;
            }
        }
        
        return null; // No win
    }
}