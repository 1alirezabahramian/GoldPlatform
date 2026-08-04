<?php

use App\Http\Controllers\Api\V1\AdminBootstrapController;
use App\Http\Controllers\Api\V1\AdminOperationalDashboardController;
use App\Http\Controllers\Api\V1\OperatorBootstrapController;
use App\Http\Controllers\Api\V1\OperatorOperationalDashboardController;
use App\Http\Controllers\Api\V1\OperatorQueueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:operator'])
    ->prefix('v1/operator')
    ->middleware('role:operator|admin')
    ->group(function () {
        Route::get('/bootstrap', OperatorBootstrapController::class);
        Route::get('/dashboard', OperatorOperationalDashboardController::class);
        Route::get('/orders/queue', [OperatorQueueController::class, 'orders']);
        Route::get('/deliveries/queue', [OperatorQueueController::class, 'deliveries']);
    });

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('v1/admin')
    ->middleware('role:admin')
    ->group(function () {
        Route::get('/bootstrap', AdminBootstrapController::class);
        Route::get('/dashboard', AdminOperationalDashboardController::class);
    });
