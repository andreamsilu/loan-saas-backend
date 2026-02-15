<?php

use App\Modules\Developer\Controllers\ApiKeyController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/keys', [ApiKeyController::class, 'index']);
    Route::post('/keys', [ApiKeyController::class, 'store']);
    Route::post('/keys/{apiKey}/rotate', [ApiKeyController::class, 'rotate']);
    Route::post('/keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke']);
});

