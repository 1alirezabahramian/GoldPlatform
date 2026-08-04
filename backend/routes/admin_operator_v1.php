<?php

use App\Http\Controllers\Api\V1\AdminAccessControlReadController;
use App\Http\Controllers\Api\V1\AdminBranchReadController;
use App\Http\Controllers\Api\V1\AdminCustodyDeliveryReadController;
use App\Http\Controllers\Api\V1\AdminCustomerGroupReadController;
use App\Http\Controllers\Api\V1\AdminDashboardController;
use App\Http\Controllers\Api\V1\AdminKimiaReadController;
use App\Http\Controllers\Api\V1\AdminNotificationReadController;
use App\Http\Controllers\Api\V1\AdminOperatorOperationalQueueController;
use App\Http\Controllers\Api\V1\AdminOrderReadController;
use App\Http\Controllers\Api\V1\AdminProductPricingReadController;
use App\Http\Controllers\Api\V1\AdminSettlementReadController;
use App\Http\Controllers\Api\V1\AdminSystemHealthReadController;
use App\Http\Controllers\Api\V1\AdminUserReadController;
use App\Http\Controllers\Api\V1\AdminWhiteLabelReadController;
use App\Http\Controllers\Api\V1\CustomerPolicyChangeRequestController;
use App\Http\Controllers\Api\V1\OperatorDashboardController;
use App\Support\AdminOperatorPermissionCatalog;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:operator'])
    ->prefix('v1/operator')
    ->middleware(['role:operator|admin', 'permission:'.AdminOperatorPermissionCatalog::OPERATOR_ACCESS])
    ->group(function () {
        Route::get('/dashboard', OperatorDashboardController::class)
            ->middleware('permission:'.AdminOperatorPermissionCatalog::OPERATOR_DASHBOARD_VIEW);
        Route::get('/orders/queue', [AdminOperatorOperationalQueueController::class, 'orders'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::ORDERS_QUEUE_VIEW);
        Route::get('/deliveries/queue', [AdminOperatorOperationalQueueController::class, 'deliveries'])
            ->middleware('permission:'.AdminOperatorPermissionCatalog::DELIVERIES_QUEUE_VIEW);
    });

Route::middleware(['auth:sanctum', 'throttle:admin'])
    ->prefix('v1/admin')
    ->middleware(['role:admin', 'permission:'.AdminOperatorPermissionCatalog::ADMIN_ACCESS])
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::ADMIN_DASHBOARD_VIEW);
        Route::get('/users', AdminUserReadController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::USERS_VIEW);
        Route::get('/customer-groups', AdminCustomerGroupReadController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::CUSTOMER_GROUPS_VIEW);
        Route::get('/roles', [AdminAccessControlReadController::class, 'roles'])->middleware('permission:'.AdminOperatorPermissionCatalog::ROLES_PERMISSIONS_VIEW);
        Route::get('/permissions', [AdminAccessControlReadController::class, 'permissions'])->middleware('permission:'.AdminOperatorPermissionCatalog::ROLES_PERMISSIONS_VIEW);
        Route::get('/access-matrix', [AdminAccessControlReadController::class, 'matrix'])->middleware('permission:'.AdminOperatorPermissionCatalog::ROLES_PERMISSIONS_VIEW);
        Route::get('/orders', [AdminOrderReadController::class, 'index'])->middleware('permission:'.AdminOperatorPermissionCatalog::ORDERS_VIEW);
        Route::get('/orders/{order}', [AdminOrderReadController::class, 'show'])->middleware('permission:'.AdminOperatorPermissionCatalog::ORDERS_DETAIL_VIEW);
        Route::get('/custodies', [AdminCustodyDeliveryReadController::class, 'custodies'])->middleware('permission:'.AdminOperatorPermissionCatalog::CUSTODIES_VIEW);
        Route::get('/custodies/{custodyAsset}', [AdminCustodyDeliveryReadController::class, 'custody'])->middleware('permission:'.AdminOperatorPermissionCatalog::CUSTODIES_DETAIL_VIEW);
        Route::get('/deliveries', [AdminCustodyDeliveryReadController::class, 'deliveries'])->middleware('permission:'.AdminOperatorPermissionCatalog::DELIVERIES_VIEW);
        Route::get('/deliveries/{deliveryRequest}', [AdminCustodyDeliveryReadController::class, 'delivery'])->middleware('permission:'.AdminOperatorPermissionCatalog::DELIVERIES_DETAIL_VIEW);
        Route::get('/settlements', [AdminSettlementReadController::class, 'index'])->middleware('permission:'.AdminOperatorPermissionCatalog::SETTLEMENTS_VIEW);
        Route::get('/settlements/{settlement}', [AdminSettlementReadController::class, 'show'])->middleware('permission:'.AdminOperatorPermissionCatalog::SETTLEMENTS_DETAIL_VIEW);
        Route::get('/kimia/overview', AdminKimiaReadController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::KIMIA_READ);
        Route::get('/system/health', AdminSystemHealthReadController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::SYSTEM_HEALTH_VIEW);
        Route::get('/product-categories', [AdminProductPricingReadController::class, 'categories'])->middleware('permission:'.AdminOperatorPermissionCatalog::PRODUCT_CATEGORIES_VIEW);
        Route::get('/products', [AdminProductPricingReadController::class, 'products'])->middleware('permission:'.AdminOperatorPermissionCatalog::PRODUCTS_VIEW);
        Route::get('/pricing/overview', [AdminProductPricingReadController::class, 'pricing'])->middleware('permission:'.AdminOperatorPermissionCatalog::PRICING_VIEW);
        Route::get('/branches', AdminBranchReadController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::BRANCHES_VIEW);
        Route::get('/white-label/overview', AdminWhiteLabelReadController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::WHITE_LABEL_VIEW);
        Route::get('/notifications/overview', AdminNotificationReadController::class)->middleware('permission:'.AdminOperatorPermissionCatalog::NOTIFICATIONS_VIEW);

        Route::get('/customer-policy-change-requests', [CustomerPolicyChangeRequestController::class, 'index'])->middleware('permission:'.AdminOperatorPermissionCatalog::POLICY_CHANGES_VIEW);
        Route::post('/customer-policy-change-requests', [CustomerPolicyChangeRequestController::class, 'store'])->middleware(['permission:'.AdminOperatorPermissionCatalog::POLICY_CHANGES_CREATE, 'idempotency:policy-change.create']);
        Route::post('/customer-policy-change-requests/{changeRequest}/submit', [CustomerPolicyChangeRequestController::class, 'submit'])->middleware(['permission:'.AdminOperatorPermissionCatalog::POLICY_CHANGES_SUBMIT, 'idempotency:policy-change.submit']);
        Route::post('/customer-policy-change-requests/{changeRequest}/approve', [CustomerPolicyChangeRequestController::class, 'approve'])->middleware(['permission:'.AdminOperatorPermissionCatalog::POLICY_CHANGES_APPROVE, 'idempotency:policy-change.approve']);
        Route::post('/customer-policy-change-requests/{changeRequest}/reject', [CustomerPolicyChangeRequestController::class, 'reject'])->middleware(['permission:'.AdminOperatorPermissionCatalog::POLICY_CHANGES_REJECT, 'idempotency:policy-change.reject']);

        Route::get('/audit-logs', [AdminOperatorOperationalQueueController::class, 'audit'])->middleware('permission:'.AdminOperatorPermissionCatalog::AUDIT_VIEW);
        Route::get('/outbox', [AdminOperatorOperationalQueueController::class, 'outbox'])->middleware('permission:'.AdminOperatorPermissionCatalog::OUTBOX_VIEW);
    });
