<?php

use App\Http\Controllers\Api\V1\AdminSettlementActionCapabilityController;
use App\Support\AdminOperatorPermissionCatalog;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('v1/admin')
    ->middleware(['role:admin', 'permission:'.AdminOperatorPermissionCatalog::ADMIN_ACCESS])
    ->group(function () {
        Route::get('/settlement-actions/overview', AdminSettlementActionCapabilityController::class)
            ->middleware('permission:'.AdminOperatorPermissionCatalog::SETTLEMENT_ACTIONS_VIEW);
    });
