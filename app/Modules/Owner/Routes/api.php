<?php

use App\Modules\Owner\Controllers\OwnerTenantController;
use App\Modules\Owner\Controllers\OwnerAnalyticsController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:' . UserRole::OWNER->value])->group(function () {
    Route::get('/tenants', [OwnerTenantController::class, 'index']);
    Route::post('/tenants/{tenant}/plan', [OwnerTenantController::class, 'setPlan']);
    Route::post('/tenants/{tenant}/suspend', [OwnerTenantController::class, 'suspend']);
    Route::post('/tenants/{tenant}/activate', [OwnerTenantController::class, 'activate']);
    Route::post('/tenants/{tenant}/billing-cycle', [OwnerTenantController::class, 'updateBillingCycle']);
    Route::get('/analytics/dashboard', [OwnerAnalyticsController::class, 'dashboard']);
});
