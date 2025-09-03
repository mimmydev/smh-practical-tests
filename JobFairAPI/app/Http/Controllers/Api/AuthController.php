<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'profession' => $request->profession,
                'experience_level' => $request->experience_level,
            ]);
            
            // Auto-login after registration
            Auth::login($user);
            
            Log::info('New user registered', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            return response()->json([
                'message' => 'Registration successful! Welcome to the Job Fair.',
                'user' => new UserResource($user)
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);
            
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => 'REGISTRATION_FAILED'
            ], 500);
        }
    }
    
    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Rate limiting is handled by middleware
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            
            $user = Auth::user();
            
            Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            return response()->json([
                'message' => 'Login successful! Welcome back.',
                'user' => new UserResource($user)
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid credentials provided.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);
            
            return response()->json([
                'message' => 'Login failed. Please try again.',
                'error' => 'LOGIN_FAILED'
            ], 500);
        }
    }
    
    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            Log::info('User logged out', ['user_id' => $userId]);
            
            return response()->json([
                'message' => 'Logged out successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'message' => 'Logout failed. Please try again.',
                'error' => 'LOGOUT_FAILED'
            ], 500);
        }
    }
    
    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user())
        ]);
    }
}
