<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiDocsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs/openapi.json', [ApiDocsController::class, 'openApi']);
Route::get('/docs', [ApiDocsController::class, 'ui']);
