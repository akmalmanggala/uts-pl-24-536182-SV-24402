<?php

use App\Http\Controllers\Api\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app'     => 'Katalog Kopi Nusantara API',
        'author'  => 'Akmal Manggala Putra (24/536182/SV/24402)',
        'class'   => 'PLBB',
        'status'  => 'Active',
    ]);
});

Route::get('/health', [HealthCheckController::class, 'check'])->name('web.health');
