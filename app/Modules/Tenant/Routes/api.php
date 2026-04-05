<?php

use App\Modules\Tenant\Controllers\TenantSettingsController;
use App\Modules\Tenant\Controllers\TenantInfoController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::get('/me', [TenantInfoController::class, 'me']);

Route::middleware(['auth:sanctum', 'role:' . UserRole::TENANT_ADMIN->value . ',' . UserRole::OWNER->value])->group(function () {
    Route::post('/settings/branding', [TenantSettingsController::class, 'updateBranding']);
    Route::post('/settings/ui-flags', [TenantSettingsController::class, 'updateUiFlags']);
    Route::post('/settings/domain', [TenantSettingsController::class, 'updateDomain']);
    Route::post('/settings/sms', [TenantSettingsController::class, 'updateSmsConfig']);
});
