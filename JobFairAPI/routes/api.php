<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExhibitorController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\GameController;

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public exhibitor registration
Route::post('/exhibitors', [ExhibitorController::class, 'store']);

// Public data access (read-only)
Route::get('/exhibitors', [ExhibitorController::class, 'index']);
Route::get('/exhibitors/{exhibitor}', [ExhibitorController::class, 'show']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

// Game routes (public access with email validation)
Route::middleware(['throttle:game'])->group(function () {
    Route::post('/game/spin', [GameController::class, 'spin']);
    Route::get('/game/prizes', [GameController::class, 'prizes']);
});

// Contact form (rate limited)
Route::middleware(['throttle:contact'])->group(function () {
    Route::post('/contact', [ContactController::class, 'store']);
});
