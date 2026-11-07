<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoffeeController;
use App\Http\Controllers\Api\HealthCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - KEPL Pertemuan 8 / P10
|--------------------------------------------------------------------------
|
| Kopi Nusantara Inventory & Health Monitoring
|
*/

// Public Authentication Routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Health Check Route (Uji aktif DB MySQL dan Subsystem Cache)
Route::get('/health', [HealthCheckController::class, 'check'])->name('api.health');

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Coffee Resource CRUD
    Route::apiResource('coffees', CoffeeController::class);
});
