<?php

namespace App\Support;

final class OperatorPermissionCatalog
{
    public const OPERATOR_ACCESS = 'operator.access';
    public const ORDERS_QUEUE_VIEW = 'orders.queue.view';
    public const DELIVERIES_QUEUE_VIEW = 'deliveries.queue.view';
    public const DELIVERIES_APPROVE = 'deliveries.approve';
    public const DELIVERIES_READY = 'deliveries.ready';
    public const DELIVERIES_COMPLETE = 'deliveries.complete';

    public static function all(): array
    {
        return [
            self::OPERATOR_ACCESS,
            self::ORDERS_QUEUE_VIEW,
            self::DELIVERIES_QUEUE_VIEW,
            self::DELIVERIES_APPROVE,
            self::DELIVERIES_READY,
            self::DELIVERIES_COMPLETE,
        ];
    }
}
