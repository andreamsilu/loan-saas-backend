<?php

use App\Modules\Developer\Controllers\ApiKeyController;
use App\Modules\Developer\Controllers\ApiUsageController;
use App\Modules\Developer\Controllers\WebhookController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'subscription', 'role:' . UserRole::TENANT_ADMIN->value . ',' . UserRole::OWNER->value])->group(function () {
    Route::get('/keys', [ApiKeyController::class, 'index']);
    Route::post('/keys', [ApiKeyController::class, 'store']);
    Route::post('/keys/{apiKey}/rotate', [ApiKeyController::class, 'rotate']);
    Route::post('/keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke']);
    Route::get('/usage', [ApiUsageController::class, 'index']);
    Route::get('/webhooks', [WebhookController::class, 'index']);
    Route::post('/webhooks', [WebhookController::class, 'store']);
    Route::put('/webhooks/{endpoint}', [WebhookController::class, 'update']);
    Route::get('/webhooks/{endpoint}/logs', [WebhookController::class, 'logs']);
});
