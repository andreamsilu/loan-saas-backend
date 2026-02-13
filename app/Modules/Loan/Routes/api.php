<?php

use App\Modules\Loan\Controllers\LoanProductController;
use App\Modules\Loan\Controllers\LoanController;
use App\Modules\Loan\Controllers\RepaymentController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Loan Product Management (Admin/Owner)
    Route::middleware(['role:' . UserRole::TENANT_ADMIN->value . ',' . UserRole::OWNER->value])->group(function () {
        Route::apiResource('products', LoanProductController::class);
    });

    // Staff can view products
    Route::middleware(['role:' . UserRole::STAFF->value])->group(function () {
        Route::get('products', [LoanProductController::class, 'index']);
        Route::get('products/{product}', [LoanProductController::class, 'show']);
    });

    // Loan Operations
    Route::prefix('loans')->group(function () {
        Route::post('/', [LoanController::class, 'store']); // Apply for loan
        Route::get('/{loan}', [LoanController::class, 'show']);
        
        // Repayments
        Route::post('/{loan}/repay', [RepaymentController::class, 'store']);
        
        // Admin/Owner only operations
        Route::middleware(['role:' . UserRole::TENANT_ADMIN->value . ',' . UserRole::OWNER->value])->group(function () {
            Route::post('/{loan}/approve', [LoanController::class, 'approve']);
            Route::post('/{loan}/disburse', [LoanController::class, 'disburse']);
        });
    });
});

