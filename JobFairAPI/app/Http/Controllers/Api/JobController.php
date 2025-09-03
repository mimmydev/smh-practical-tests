<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    /**
     * Display a listing of jobs (Public)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Job::with(['exhibitor' => function($q) {
                $q->where('status', 'approved');
            }])->active()
            ->whereHas('exhibitor', function($q) {
                $q->where('status', 'approved'); // Only jobs from approved exhibitors
            });
            
            // Filter by job type
            if ($request->has('job_type') && $request->job_type) {
                $query->byType($request->job_type);
            }
            
            // Filter by exhibitor
            if ($request->has('exhibitor_id') && $request->exhibitor_id) {
                $query->where('exhibitor_id', $request->exhibitor_id);
            }
            
            // Filter by location
            if ($request->has('location') && $request->location) {
                $query->where('location', 'like', "%{$request->location}%");
            }
            
            // Search by title, description, or skills
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('requirements', 'like', "%{$search}%")
                      ->orWhereJsonContains('skills_required', $search);
                });
            }
            
            // Filter by availability
            if ($request->has('available_only') && $request->available_only) {
                $query->whereRaw('available_slots > (
                    SELECT COUNT(*) FROM reservations 
                    WHERE job_id = jobs.id AND status = "confirmed"
                )');
            }
            
            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['title', 'job_type', 'created_at', 'available_slots'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
            
            $perPage = min($request->get('per_page', 15), 50);
            $jobs = $query->paginate($perPage);
            
            return response()->json([
                'data' => JobResource::collection($jobs->items()),
                'meta' => [
                    'current_page' => $jobs->currentPage(),
                    'total' => $jobs->total(),
                    'per_page' => $jobs->perPage(),
                    'last_page' => $jobs->lastPage(),
                ],
                'filters' => [
                    'job_types' => ['full_time', 'part_time', 'contract', 'internship'],
                    'locations' => Job::active()->distinct()->pluck('location')->filter()->values(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch jobs', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);
            
            return response()->json([
                'message' => 'Failed to load job listings. Please try again.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
    
    /**
     * Display the specified job
     */
    public function show(Job $job): JsonResponse
    {
        try {
            // Only show active jobs from approved exhibitors
            if (!$job->is_active || $job->exhibitor->status !== 'approved') {
                return response()->json([
                    'message' => 'Job not found or not available.',
                    'error' => 'NOT_FOUND'
                ], 404);
            }
            
            $job->load(['exhibitor', 'reservations' => function($q) {
                $q->where('status', 'confirmed');
            }]);
            
            return response()->json([
                'data' => new JobResource($job),
                'related_jobs' => JobResource::collection(
                    Job::active()
                       ->where('exhibitor_id', $job->exhibitor_id)
                       ->where('id', '!=', $job->id)
                       ->limit(3)
                       ->get()
                )
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch job details', [
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to load job details.',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
    
    /**
     * Store a newly created job (Admin/Exhibitor only)
     */
    public function store(CreateJobRequest $request): JsonResponse
    {
        try {
            $job = Job::create($request->validated());
            
            Log::info('New job created', [
                'job_id' => $job->id,
                'exhibitor_id' => $job->exhibitor_id,
                'title' => $job->title
            ]);
            
            return response()->json([
                'message' => 'Job posted successfully!',
                'data' => new JobResource($job->load('exhibitor'))
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Job creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return response()->json([
                'message' => 'Failed to create job posting.',
                'error' => 'CREATION_FAILED'
            ], 500);
        }
    }
}