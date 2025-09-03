<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Job;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    /**
     * Display user's reservations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            $query = $user->reservations()
                         ->with(['job.exhibitor'])
                         ->orderBy('session_time', 'asc');
            
            // Filter by status
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }
            
            // Filter by session type
            if ($request->has('session_type')) {
                $query->bySessionType($request->session_type);
            }
            
            // Filter by upcoming/past
            if ($request->has('upcoming') && $request->upcoming) {
                $query->upcoming();
            }
            
            $reservations = $query->paginate(10);
            
            return response()->json([
                'data' => ReservationResource::collection($reservations->items()),
                'meta' => [
                    'current_page' => $reservations->currentPage(),
                    'total' => $reservations->total(),
                    'per_page' => $reservations->perPage(),
                    'last_page' => $reservations->lastPage(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch user reservations', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to load your reservations.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
    
    /**
     * Create new reservation
     */
    public function store(CreateReservationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $job = Job::findOrFail($request->job_id);
            /** @var User $user */
            $user = Auth::user();
            
            // Check if job is available
            if (!$job->is_active || $job->exhibitor->status !== 'approved') {
                return response()->json([
                    'message' => 'This job is no longer available for reservations.',
                    'error' => 'JOB_UNAVAILABLE'
                ], 400);
            }
            
            // Check if user already has a reservation for this job
            $existingReservation = Reservation::where('user_id', $user->id)
                                             ->where('job_id', $job->id)
                                             ->where('status', '!=', 'cancelled')
                                             ->first();
            
            if ($existingReservation) {
                return response()->json([
                    'message' => 'You already have a reservation for this job.',
                    'error' => 'DUPLICATE_RESERVATION'
                ], 400);
            }
            
            // Check if slots are available
            if (!$job->hasAvailableSlots()) {
                return response()->json([
                    'message' => 'Sorry, all slots for this job are fully booked.',
                    'error' => 'NO_SLOTS_AVAILABLE'
                ], 400);
            }
            
            // Validate session time
            $sessionTime = Carbon::parse($request->session_time);
            if ($sessionTime->isPast()) {
                return response()->json([
                    'message' => 'Cannot book a session in the past.',
                    'error' => 'INVALID_TIME'
                ], 400);
            }
            
            // Create reservation
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'job_id' => $job->id,
                'session_type' => $request->session_type,
                'session_time' => $sessionTime,
                'status' => 'confirmed', // Auto-confirm for now
                'user_message' => $request->user_message,
                'additional_info' => $request->additional_info,
            ]);
            
            DB::commit();
            
            Log::info('New reservation created', [
                'reservation_id' => $reservation->id,
                'user_id' => $user->id,
                'job_id' => $job->id,
                'session_time' => $sessionTime
            ]);
            
            return response()->json([
                'message' => 'Reservation confirmed! You will receive a confirmation email shortly.',
                'data' => new ReservationResource($reservation->load(['job.exhibitor']))
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Reservation creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return response()->json([
                'message' => 'Failed to create reservation. Please try again.',
                'error' => 'RESERVATION_FAILED'
            ], 500);
        }
    }
    
    /**
     * Cancel reservation
     */
    public function destroy(Reservation $reservation): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            // Check if user owns this reservation
            if ($reservation->user_id !== $user->id) {
                return response()->json([
                    'message' => 'You can only cancel your own reservations.',
                    'error' => 'UNAUTHORIZED'
                ], 403);
            }
            
            // Check if can be cancelled (not in the past and not already completed)
            if ($reservation->session_time->isPast() || $reservation->status === 'completed') {
                return response()->json([
                    'message' => 'Cannot cancel past or completed reservations.',
                    'error' => 'CANNOT_CANCEL'
                ], 400);
            }
            
            $reservation->status = 'cancelled';
            $reservation->save();
            
            Log::info('Reservation cancelled', [
                'reservation_id' => $reservation->id,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'message' => 'Reservation cancelled successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Reservation cancellation failed', [
                'reservation_id' => $reservation->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to cancel reservation.',
                'error' => 'CANCELLATION_FAILED'
            ], 500);
        }
    }
    
    /**
     * Get user's upcoming reservations (shortcut method)
     */
    public function myReservations(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            $upcoming = $user->upcomingReservations()
                            ->with(['job.exhibitor'])
                            ->orderBy('session_time', 'asc')
                            ->get();
            
            return response()->json([
                'data' => ReservationResource::collection($upcoming),
                'count' => $upcoming->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch upcoming reservations', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to load your upcoming reservations.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
}
