<?php

use App\Http\Controllers\Api\V1\AdminOperatorOperationalQueueController;
use App\Support\AdminOperatorPermissionCatalog;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:operator'])
    ->prefix('v1/operator')
    ->middleware(['role:operator|admin', 'permission:'.AdminOperatorPermissionCatalog::OPERATOR_ACCESS])
    ->group(function () {
        Route::get('/orders/queue', [AdminOperatorOperationalQueueController::class, 'orders'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::ORDERS_QUEUE_VIEW);
        Route::get('/deliveries/queue', [AdminOperatorOperationalQueueController::class, 'deliveries'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::DELIVERIES_QUEUE_VIEW);
    });

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('v1/admin')
    ->middleware(['role:admin', 'permission:'.AdminOperatorPermissionCatalog::ADMIN_ACCESS])
    ->group(function () {
        Route::get('/audit-logs', [AdminOperatorOperationalQueueController::class, 'audit'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::AUDIT_VIEW);
        Route::get('/outbox', [AdminOperatorOperationalQueueController::class, 'outbox'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::OUTBOX_VIEW);
    });
