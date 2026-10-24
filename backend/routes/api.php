<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoffeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - UTS KEPL 2026
|--------------------------------------------------------------------------
|
| Kopi Nusantara Inventory & Roastery Management System
|
*/

// Public Authentication Routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Health Check Route
Route::get('/health', function () {
    return response()->json([
        'status'  => 'OK',
        'service' => 'Kopi Nusantara API',
        'server'  => 'FrankenPHP / Laravel 11',
        'time'    => now()->toIso8601String(),
    ]);
});

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Coffee Resource CRUD
    Route::apiResource('coffees', CoffeeController::class);
});
