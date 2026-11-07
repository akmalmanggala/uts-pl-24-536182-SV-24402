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

// Coffee Endpoints (Dapat diakses langsung untuk pengujian logging & transaksi)
Route::get('/coffees', [CoffeeController::class, 'index'])->name('api.coffees.index');
Route::post('/coffees', [CoffeeController::class, 'store'])->name('api.coffees.store');
Route::get('/coffees/{id}', [CoffeeController::class, 'show'])->name('api.coffees.show');

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Update and Delete Coffee Resources
    Route::put('/coffees/{id}', [CoffeeController::class, 'update'])->name('api.coffees.update');
    Route::delete('/coffees/{id}', [CoffeeController::class, 'destroy'])->name('api.coffees.destroy');
});
