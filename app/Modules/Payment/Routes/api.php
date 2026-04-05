<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payment\Http\Controllers\AggregatorWebhookController;

Route::prefix('aggregator')->group(function () {
    Route::match(['GET', 'POST'], 'ipn', [AggregatorWebhookController::class, 'ipn']);
});

