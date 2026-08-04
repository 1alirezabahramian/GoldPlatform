<?php

use App\Http\Controllers\Api\V1\AdminBootstrapController;
use App\Http\Controllers\Api\V1\OperatorBootstrapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:operator'])
    ->prefix('v1/operator')
    ->middleware('role:operator|admin')
    ->group(function () {
        Route::get('/bootstrap', OperatorBootstrapController::class);
    });

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('v1/admin')
    ->middleware('role:admin')
    ->group(function () {
        Route::get('/bootstrap', AdminBootstrapController::class);
    });
