<?php

namespace App\Support;

final class AdminOperatorPermissionCatalog
{
    public const ADMIN_ACCESS = 'admin.access';
    public const OPERATOR_ACCESS = 'operator.access';

    public const ADMIN_DASHBOARD_VIEW = 'admin.dashboard.view';
    public const OPERATOR_DASHBOARD_VIEW = 'operator.dashboard.view';

    public const USERS_VIEW = 'users.view';
    public const CUSTOMER_GROUPS_VIEW = 'customer-groups.view';
    public const ROLES_PERMISSIONS_VIEW = 'roles-permissions.view';
    public const ORDERS_VIEW = 'orders.view';
    public const ORDERS_DETAIL_VIEW = 'orders.detail.view';
    public const CUSTODIES_VIEW = 'custodies.view';
    public const CUSTODIES_DETAIL_VIEW = 'custodies.detail.view';
    public const DELIVERIES_VIEW = 'deliveries.view';
    public const DELIVERIES_DETAIL_VIEW = 'deliveries.detail.view';
    public const SETTLEMENTS_VIEW = 'settlements.view';
    public const SETTLEMENTS_DETAIL_VIEW = 'settlements.detail.view';
    public const KIMIA_READ = 'kimia.read';
    public const SYSTEM_HEALTH_VIEW = 'system-health.view';
    public const PRODUCTS_VIEW = 'products.view';
    public const PRODUCT_CATEGORIES_VIEW = 'product-categories.view';
    public const PRICING_VIEW = 'pricing.view';
    public const BRANCHES_VIEW = 'branches.view';
    public const WHITE_LABEL_VIEW = 'white-label.view';
    public const NOTIFICATIONS_VIEW = 'notifications.view';
    public const REPORTS_VIEW = 'reports.view';
    public const POLICY_CHANGES_VIEW = 'customer-policy-changes.view';
    public const POLICY_CHANGES_CREATE = 'customer-policy-changes.create';
    public const POLICY_CHANGES_SUBMIT = 'customer-policy-changes.submit';
    public const POLICY_CHANGES_APPROVE = 'customer-policy-changes.approve';
    public const POLICY_CHANGES_REJECT = 'customer-policy-changes.reject';
    public const AUDIT_VIEW = 'audit.view';
    public const OUTBOX_VIEW = 'outbox.view';
    public const CUSTOMER_POLICIES_VIEW = 'customer-policies.view';
    public const CUSTOMER_POLICIES_UPDATE = 'customer-policies.update';

    public const ORDERS_QUEUE_VIEW = 'orders.queue.view';
    public const DELIVERIES_QUEUE_VIEW = 'deliveries.queue.view';
    public const DELIVERIES_APPROVE = 'deliveries.approve';
    public const DELIVERIES_READY = 'deliveries.ready';
    public const DELIVERIES_COMPLETE = 'deliveries.complete';

    public static function all(): array
    {
        return [
            self::ADMIN_ACCESS,
            self::OPERATOR_ACCESS,
            self::ADMIN_DASHBOARD_VIEW,
            self::OPERATOR_DASHBOARD_VIEW,
            self::USERS_VIEW,
            self::CUSTOMER_GROUPS_VIEW,
            self::ROLES_PERMISSIONS_VIEW,
            self::ORDERS_VIEW,
            self::ORDERS_DETAIL_VIEW,
            self::CUSTODIES_VIEW,
            self::CUSTODIES_DETAIL_VIEW,
            self::DELIVERIES_VIEW,
            self::DELIVERIES_DETAIL_VIEW,
            self::SETTLEMENTS_VIEW,
            self::SETTLEMENTS_DETAIL_VIEW,
            self::KIMIA_READ,
            self::SYSTEM_HEALTH_VIEW,
            self::PRODUCTS_VIEW,
            self::PRODUCT_CATEGORIES_VIEW,
            self::PRICING_VIEW,
            self::BRANCHES_VIEW,
            self::WHITE_LABEL_VIEW,
            self::NOTIFICATIONS_VIEW,
            self::REPORTS_VIEW,
            self::POLICY_CHANGES_VIEW,
            self::POLICY_CHANGES_CREATE,
            self::POLICY_CHANGES_SUBMIT,
            self::POLICY_CHANGES_APPROVE,
            self::POLICY_CHANGES_REJECT,
            self::AUDIT_VIEW,
            self::OUTBOX_VIEW,
            self::CUSTOMER_POLICIES_VIEW,
            self::CUSTOMER_POLICIES_UPDATE,
            self::ORDERS_QUEUE_VIEW,
            self::DELIVERIES_QUEUE_VIEW,
            self::DELIVERIES_APPROVE,
            self::DELIVERIES_READY,
            self::DELIVERIES_COMPLETE,
        ];
    }

    public static function adminDefaults(): array
    {
        return self::all();
    }

    public static function operatorDefaults(): array
    {
        return [
            self::OPERATOR_ACCESS,
            self::OPERATOR_DASHBOARD_VIEW,
            self::ORDERS_QUEUE_VIEW,
            self::DELIVERIES_QUEUE_VIEW,
            self::DELIVERIES_APPROVE,
            self::DELIVERIES_READY,
            self::DELIVERIES_COMPLETE,
        ];
    }
}
