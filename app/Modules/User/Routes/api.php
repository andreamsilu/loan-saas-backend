<?php

use App\Modules\User\Controllers\AuthController;
use App\Modules\User\Controllers\StaffController;
use App\Modules\User\Controllers\RolePermissionController;
use App\Modules\User\Controllers\UserRoleAssignmentController;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:' . UserRole::TENANT_ADMIN->value . ',' . UserRole::OWNER->value)->group(function () {
        Route::get('/staff', [StaffController::class, 'index']);
        Route::post('/staff/{user}/role', [StaffController::class, 'updateRole']);

        Route::get('/role-permissions', [RolePermissionController::class, 'index']);
        Route::post('/role-permissions', [RolePermissionController::class, 'store']);
        Route::delete('/role-permissions/{rolePermission}', [RolePermissionController::class, 'destroy']);

        Route::get('/{user}/roles', [UserRoleAssignmentController::class, 'index']);
        Route::post('/{user}/roles', [UserRoleAssignmentController::class, 'store']);
        Route::delete('/{user}/roles/{role}', [UserRoleAssignmentController::class, 'destroy']);
    });
});
