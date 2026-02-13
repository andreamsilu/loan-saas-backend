<?php

use App\Modules\Borrower\Controllers\BorrowerController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [BorrowerController::class, 'index']);
    Route::post('/', [BorrowerController::class, 'store']);
    Route::get('/{borrower}', [BorrowerController::class, 'show']);
    Route::put('/{borrower}', [BorrowerController::class, 'update']);
    
    // Only tenant_admin or owner can blacklist
    Route::post('/{borrower}/blacklist', [BorrowerController::class, 'blacklist'])
         ->middleware('role:' . UserRole::TENANT_ADMIN->value . ',' . UserRole::OWNER->value);
});

