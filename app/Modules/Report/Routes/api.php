<?php

use App\Modules\Report\Controllers\OperationalReportController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'subscription', 'role:' . UserRole::TENANT_ADMIN->value . ',' . UserRole::OWNER->value])->group(function () {
    Route::get('/dashboard', [OperationalReportController::class, 'dashboard']);
    Route::get('/disbursement-trends', [OperationalReportController::class, 'disbursementTrends']);
});
