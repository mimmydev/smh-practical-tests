<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExhibitorController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\GameController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/contact', [ContactController::class, 'store']);

// Public exhibitor registration
Route::post('/exhibitors', [ExhibitorController::class, 'store']);

// Public data (read-only)
Route::get('/exhibitors', [ExhibitorController::class, 'index']);
Route::get('/exhibitors/{exhibitor}', [ExhibitorController::class, 'show']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

// Game routes (can be public or require email)
Route::post('/game/spin', [GameController::class, 'spin']);
Route::get('/game/prizes', [GameController::class, 'prizes']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Reservations (user must be authenticated)
    Route::apiResource('reservations', ReservationController::class);
    
    // User-specific routes
    Route::get('/my-reservations', [ReservationController::class, 'myReservations']);
    
    // Admin routes (add role-based middleware later)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/exhibitors', [ExhibitorController::class, 'adminIndex']);
        Route::put('/admin/exhibitors/{exhibitor}/approve', [ExhibitorController::class, 'approve']);
        Route::get('/admin/contacts', [ContactController::class, 'index']);
    });
});

// Rate limited routes
Route::middleware(['throttle:contact'])->group(function () {
    Route::post('/contact', [ContactController::class, 'store']);
});