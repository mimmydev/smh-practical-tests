<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Store a newly created contact submission
     */
    public function store(ContactFormRequest $request): JsonResponse
    {
        try {
            $contact = ContactSubmission::create($request->validated());
            
            // Log for admin notification
            Log::info('New contact submission received', [
                'id' => $contact->id,
                'category' => $contact->category,
                'email' => $contact->email
            ]);
            
            return response()->json([
                'message' => 'Thank you for your message. We will get back to you soon!',
                'data' => [
                    'id' => $contact->id,
                    'status' => $contact->status
                ]
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return response()->json([
                'message' => 'Sorry, there was an error submitting your message. Please try again.',
                'error' => 'SUBMISSION_FAILED'
            ], 500);
        }
    }
    
    /**
     * Get all contact submissions (Admin only - TODO: add middleware later)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ContactSubmission::query();
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }
            
            // Search by name or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%");
                });
            }
            
            $contacts = $query->orderBy('created_at', 'desc')
                            ->paginate(20);
            
            return response()->json($contacts);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch contact submissions', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to fetch contact submissions',
                'error' => 'FETCH_FAILED'
            ], 500);
        }
    }
}