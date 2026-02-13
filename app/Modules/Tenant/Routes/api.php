<?php

use Illuminate\Support\Facades\Route;

Route::get('/me', function () {
    return response()->json([
        'tenant' => app(\App\Shared\Services\TenantManager::class)->getTenant()
    ]);
});
