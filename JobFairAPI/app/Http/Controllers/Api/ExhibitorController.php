<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExhibitorRequest;
use App\Http\Resources\ExhibitorResource;
use App\Models\Exhibitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ExhibitorController extends Controller
{
    /**
     * Display a listing of exhibitors (Public)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Exhibitor::with(['jobs' => function($q) {
                $q->where('is_active', true);
            }])->approved(); // Only show approved exhibitors to public
            
            // Filter by industry
            if ($request->has('industry') && $request->industry) {
                $query->byIndustry($request->industry);
            }
            
            // Search by name or description
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('industry', 'like', "%{$search}%");
                });
            }
            
            // Sort options
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['name', 'industry', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
            
            $perPage = min($request->get('per_page', 12), 50); // Max 50 per page
            $exhibitors = $query->paginate($perPage);
            
            return response()->json([
                'data' => ExhibitorResource::collection($exhibitors->items()),
                'meta' => [
                    'current_page' => $exhibitors->currentPage(),
                    'total' => $exhibitors->total(),
                    'per_page' => $exhibitors->perPage(),
                    'last_page' => $exhibitors->lastPage(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch exhibitors', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);
            
            return response()->json([
                'message' => 'Failed to load exhibitors. Please try again.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
    
    /**
     * Store a newly created exhibitor (Public Registration)
     */
    public function store(CreateExhibitorRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $exhibitor = Exhibitor::create([
                ...$request->validated(),
                'status' => 'pending' // Always pending for manual approval
            ]);
            
            DB::commit();
            
            // Log for admin notification
            Log::info('New exhibitor registration', [
                'exhibitor_id' => $exhibitor->id,
                'name' => $exhibitor->name,
                'email' => $exhibitor->contact_email
            ]);
            
            return response()->json([
                'message' => 'Registration submitted successfully! We will review your application and contact you within 48 hours.',
                'data' => new ExhibitorResource($exhibitor)
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Exhibitor registration failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return response()->json([
                'message' => 'Registration failed. Please check your information and try again.',
                'error' => 'REGISTRATION_FAILED'
            ], 500);
        }
    }
    
    /**
     * Display the specified exhibitor
     */
    public function show(Exhibitor $exhibitor): JsonResponse
    {
        try {
            // Only show approved exhibitors to public
            if ($exhibitor->status !== 'approved') {
                return response()->json([
                    'message' => 'Exhibitor not found or not available.',
                    'error' => 'NOT_FOUND'
                ], 404);
            }
            
            $exhibitor->load(['jobs' => function($q) {
                $q->where('is_active', true)->with('reservations');
            }]);
            
            return response()->json([
                'data' => new ExhibitorResource($exhibitor)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch exhibitor details', [
                'exhibitor_id' => $exhibitor->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to load exhibitor details.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
    
    /**
     * Admin: Get all exhibitors including pending ones
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Exhibitor::with('jobs');
            
            // Filter by status (including pending)
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            $exhibitors = $query->orderBy('created_at', 'desc')
                               ->paginate(20);
            
            return response()->json([
                'data' => ExhibitorResource::collection($exhibitors->items()),
                'meta' => [
                    'current_page' => $exhibitors->currentPage(),
                    'total' => $exhibitors->total(),
                    'per_page' => $exhibitors->perPage(),
                    'last_page' => $exhibitors->lastPage(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch exhibitors for admin', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to load exhibitors.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
    
    /**
     * Admin: Approve exhibitor
     */
    public function approve(Exhibitor $exhibitor, Request $request): JsonResponse
    {
        try {
            $action = $request->input('action'); // 'approve' or 'reject'
            
            if (!in_array($action, ['approve', 'reject'])) {
                return response()->json([
                    'message' => 'Invalid action. Use "approve" or "reject".',
                    'error' => 'INVALID_ACTION'
                ], 400);
            }
            
            $exhibitor->status = $action === 'approve' ? 'approved' : 'rejected';
            $exhibitor->save();
            
            Log::info("Exhibitor {$action}d", [
                'exhibitor_id' => $exhibitor->id,
                'name' => $exhibitor->name,
                'action' => $action,
                'admin_id' => Auth::id()
            ]);
            
            $message = $action === 'approve' 
                ? 'Exhibitor approved successfully!' 
                : 'Exhibitor rejected.';
            
            return response()->json([
                'message' => $message,
                'data' => new ExhibitorResource($exhibitor)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update exhibitor status', [
                'exhibitor_id' => $exhibitor->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to update exhibitor status.',
                'error' => 'UPDATE_FAILED'
            ], 500);
        }
    }
}
