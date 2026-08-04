<?php

use App\Http\Controllers\Api\AdminPanelController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerPanelController;
use App\Http\Controllers\Api\KimiaController;
use App\Http\Controllers\Api\OperatorPanelController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\V1\AdminDashboardController;
use App\Http\Controllers\Api\V1\CustomerActivityController;
use App\Http\Controllers\Api\V1\CustomerAssetReadController;
use App\Http\Controllers\Api\V1\CustomerCustodyDeliveryController;
use App\Http\Controllers\Api\V1\CustomerDashboardController;
use App\Http\Controllers\Api\V1\CustomerOrderStatusController;
use App\Http\Controllers\Api\V1\CustomerProfileController;
use App\Http\Controllers\Api\V1\CustomerReadController;
use App\Http\Controllers\Api\V1\OperatorDashboardController;
use App\Support\AdminOperatorPermissionCatalog;
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
        Route::get('/activities', CustomerActivityController::class);
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
        Route::get('/profile', CustomerProfileController::class);
    });
});

Route::middleware(['auth:sanctum', 'throttle:operator'])
    ->prefix('operator')->middleware(['role:operator|admin', 'permission:'.AdminOperatorPermissionCatalog::OPERATOR_ACCESS])->group(function () {
        Route::get('/orders/queue', [OperatorPanelController::class, 'orderQueue'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::ORDERS_QUEUE_VIEW);
        Route::get('/deliveries/queue', [OperatorPanelController::class, 'deliveryQueue'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::DELIVERIES_QUEUE_VIEW);
        Route::post('/deliveries/{deliveryRequest}/approve', [OperatorPanelController::class, 'approveDelivery'])
            ->middleware(['permission:'.AdminOperatorPermissionCatalog::DELIVERIES_APPROVE, 'idempotency:delivery.approve']);
        Route::post('/deliveries/{deliveryRequest}/ready', [OperatorPanelController::class, 'markDeliveryReady'])
            ->middleware(['permission:'.AdminOperatorPermissionCatalog::DELIVERIES_READY, 'idempotency:delivery.ready']);
        Route::post('/deliveries/{deliveryRequest}/deliver', [OperatorPanelController::class, 'deliver'])
            ->middleware(['permission:'.AdminOperatorPermissionCatalog::DELIVERIES_COMPLETE, 'idempotency:delivery.deliver']);
    });

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('admin')->middleware(['role:admin', 'permission:'.AdminOperatorPermissionCatalog::ADMIN_ACCESS])->group(function () {
        Route::get('/audit-logs', [AdminPanelController::class, 'auditLogs'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::AUDIT_VIEW);
        Route::get('/outbox', [AdminPanelController::class, 'outbox'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::OUTBOX_VIEW);
        Route::get('/customer-policies', [AdminPanelController::class, 'policies'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::CUSTOMER_POLICIES_VIEW);
        Route::put('/customer-policies/{policy}', [AdminPanelController::class, 'updatePolicy'])
            ->middleware(['permission:'.AdminOperatorPermissionCatalog::CUSTOMER_POLICIES_UPDATE, 'idempotency:policy.update']);
    });

Route::middleware(['auth:sanctum', 'throttle:operator'])
    ->prefix('v1/operator')
    ->middleware(['role:operator|admin', 'permission:'.AdminOperatorPermissionCatalog::OPERATOR_ACCESS])
    ->group(function () {
        Route::get('/dashboard', OperatorDashboardController::class)
            ->middleware('permission:'.AdminOperatorPermissionCatalog::OPERATOR_DASHBOARD_VIEW);
    });

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('v1/admin')
    ->middleware(['role:admin', 'permission:'.AdminOperatorPermissionCatalog::ADMIN_ACCESS])
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)
            ->middleware('permission:'.AdminOperatorPermissionCatalog::ADMIN_DASHBOARD_VIEW);
    });

Route::prefix('kimia')->middleware('throttle:public-read')->group(function () {
    Route::get('/account-groups', [KimiaController::class, 'accountGroups']);
});

if (file_exists(__DIR__.'/kimia.php')) require __DIR__.'/kimia.php';
