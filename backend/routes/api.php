<?php

use App\Http\Controllers\Api\AdminPanelController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerPanelController;
use App\Http\Controllers\Api\KimiaController;
use App\Http\Controllers\Api\OperatorPanelController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\V1\CustomerAssetReadController;
use App\Http\Controllers\Api\V1\CustomerCustodyDeliveryController;
use App\Http\Controllers\Api\V1\CustomerDashboardController;
use App\Http\Controllers\Api\V1\CustomerOrderStatusController;
use App\Http\Controllers\Api\V1\CustomerProfileController;
use App\Http\Controllers\Api\V1\CustomerReadController;
use App\Support\OperatorPermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/register', [RegistrationController::class, 'register']);
});

Route::middleware(['auth:sanctum', 'throttle:customer'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/orders', [OrderController::class, 'store'])->middleware('idempotency:order.create');

    Route::prefix('customer')->middleware('role:customer')->group(function () {
        Route::get('/overview', [CustomerPanelController::class, 'overview']);
        Route::get('/orders', [CustomerPanelController::class, 'orders']);
        Route::get('/custody', [CustomerPanelController::class, 'custody']);
        Route::get('/deliveries', [CustomerPanelController::class, 'deliveries']);
        Route::post('/custody/{custodyAsset}/delivery', [CustomerPanelController::class, 'requestDelivery'])
            ->middleware('idempotency:delivery.request');
    });

    Route::prefix('v1/customer')->middleware('role:customer')->group(function () {
        Route::get('/dashboard', CustomerDashboardController::class);
        Route::get('/profile', CustomerProfileController::class);
        Route::get('/assets', [CustomerAssetReadController::class, 'index']);
        Route::get('/assets/money', [CustomerAssetReadController::class, 'money']);
        Route::get('/assets/gold', [CustomerAssetReadController::class, 'gold']);
        Route::get('/assets/coins', [CustomerAssetReadController::class, 'coins']);
        Route::get('/assets/currencies', [CustomerAssetReadController::class, 'currencies']);
        Route::get('/orders', [CustomerReadController::class, 'orders']);
        Route::get('/order-statuses', CustomerOrderStatusController::class);
        Route::get('/custodies', [CustomerReadController::class, 'custodies']);
        Route::get('/custodies/{reference}', [CustomerCustodyDeliveryController::class, 'showCustody']);
        Route::post('/custodies/{reference}/delivery-request', [CustomerCustodyDeliveryController::class, 'requestDelivery'])
            ->middleware('idempotency:delivery.request');
        Route::get('/deliveries', [CustomerReadController::class, 'deliveries']);
        Route::get('/deliveries/{reference}', [CustomerCustodyDeliveryController::class, 'showDelivery']);
    });
});

Route::middleware(['auth:sanctum', 'throttle:operator'])
    ->prefix('operator')
    ->middleware(['role:operator|admin', 'permission:'.OperatorPermissionCatalog::OPERATOR_ACCESS])
    ->group(function () {
        Route::get('/orders/queue', [OperatorPanelController::class, 'orderQueue'])
            ->middleware('permission:'.OperatorPermissionCatalog::ORDERS_QUEUE_VIEW);
        Route::get('/deliveries/queue', [OperatorPanelController::class, 'deliveryQueue'])
            ->middleware('permission:'.OperatorPermissionCatalog::DELIVERIES_QUEUE_VIEW);
        Route::post('/deliveries/{deliveryRequest}/approve', [OperatorPanelController::class, 'approveDelivery'])
            ->middleware(['permission:'.OperatorPermissionCatalog::DELIVERIES_APPROVE, 'idempotency:delivery.approve']);
        Route::post('/deliveries/{deliveryRequest}/ready', [OperatorPanelController::class, 'markDeliveryReady'])
            ->middleware(['permission:'.OperatorPermissionCatalog::DELIVERIES_READY, 'idempotency:delivery.ready']);
        Route::post('/deliveries/{deliveryRequest}/deliver', [OperatorPanelController::class, 'deliver'])
            ->middleware(['permission:'.OperatorPermissionCatalog::DELIVERIES_COMPLETE, 'idempotency:delivery.deliver']);
    });

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/audit-logs', [AdminPanelController::class, 'auditLogs']);
        Route::get('/outbox', [AdminPanelController::class, 'outbox']);
        Route::get('/customer-policies', [AdminPanelController::class, 'policies']);
        Route::put('/customer-policies/{policy}', [AdminPanelController::class, 'updatePolicy'])
            ->middleware('idempotency:policy.update');
    });

Route::prefix('kimia')->middleware('throttle:public-read')->group(function () {
    Route::get('/account-groups', [KimiaController::class, 'accountGroups']);
});

if (file_exists(__DIR__.'/kimia.php')) require __DIR__.'/kimia.php';
