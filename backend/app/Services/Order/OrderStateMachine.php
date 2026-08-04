<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use LogicException;

class OrderStateMachine
{
    /** @var array<string, list<OrderStatus>> */
    private const TRANSITIONS = [
        OrderStatus::Pending->value => [
            OrderStatus::Approved,
            OrderStatus::Rejected,
            OrderStatus::Cancelled,
            OrderStatus::Expired,
        ],
        OrderStatus::Approved->value => [
            OrderStatus::Executing,
            OrderStatus::Cancelled,
            OrderStatus::Expired,
        ],
        OrderStatus::Executing->value => [
            OrderStatus::Settling,
            OrderStatus::Failed,
        ],
        OrderStatus::Settling->value => [
            OrderStatus::Completed,
            OrderStatus::Failed,
        ],
    ];

    public function approve(Order $order): Order
    {
        return $this->transition($order, OrderStatus::Approved);
    }

    public function startExecution(Order $order): Order
    {
        return $this->transition($order, OrderStatus::Executing);
    }

    public function startSettlement(Order $order): Order
    {
        return $this->transition($order, OrderStatus::Settling);
    }

    public function complete(Order $order): Order
    {
        return $this->transition($order, OrderStatus::Completed);
    }

    public function reject(Order $order, string $reason): Order
    {
        return $this->transition($order, OrderStatus::Rejected, $this->requiredReason($reason));
    }

    public function cancel(Order $order, ?string $reason = null): Order
    {
        return $this->transition($order, OrderStatus::Cancelled, $reason);
    }

    public function fail(Order $order, string $reason): Order
    {
        return $this->transition($order, OrderStatus::Failed, $this->requiredReason($reason));
    }

    public function expire(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $locked = $this->lock($order);

            if ($locked->status === OrderStatus::Expired) {
                return $locked;
            }

            if ($locked->expires_at === null || $locked->expires_at->isFuture()) {
                throw new LogicException("Order {$locked->id} is not expired yet.");
            }

            return $this->apply($locked, OrderStatus::Expired, 'Order price lock expired.');
        });
    }

    public function transition(Order $order, OrderStatus $target, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($order, $target, $reason): Order {
            $locked = $this->lock($order);

            if ($locked->status === $target) {
                return $locked;
            }

            $allowed = self::TRANSITIONS[$locked->status->value] ?? [];

            if (! in_array($target, $allowed, true)) {
                throw new LogicException(
                    "Order {$locked->id} cannot transition from {$locked->status->value} to {$target->value}."
                );
            }

            if ($locked->status->isTerminal()) {
                throw new LogicException("Terminal order {$locked->id} cannot transition.");
            }

            if ($target === OrderStatus::Expired) {
                if ($locked->expires_at === null || $locked->expires_at->isFuture()) {
                    throw new LogicException("Order {$locked->id} is not expired yet.");
                }
            }

            if (in_array($target, [OrderStatus::Rejected, OrderStatus::Failed], true)) {
                $reason = $this->requiredReason((string) $reason);
            }

            return $this->apply($locked, $target, $reason);
        });
    }

    private function apply(Order $order, OrderStatus $target, ?string $reason): Order
    {
        $timestampColumn = match ($target) {
            OrderStatus::Approved => 'approved_at',
            OrderStatus::Executing => 'executing_at',
            OrderStatus::Settling => 'settling_at',
            OrderStatus::Completed => 'completed_at',
            OrderStatus::Rejected => 'rejected_at',
            OrderStatus::Cancelled => 'cancelled_at',
            OrderStatus::Failed => 'failed_at',
            OrderStatus::Expired => 'expired_at',
            default => null,
        };

        $attributes = [
            'status' => $target,
            'status_reason' => $reason,
            'state_version' => $order->state_version + 1,
        ];

        if ($timestampColumn !== null) {
            $attributes[$timestampColumn] = now();
        }

        $order->forceFill($attributes)->save();

        return $order->refresh();
    }

    private function lock(Order $order): Order
    {
        return Order::query()
            ->whereKey($order->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function requiredReason(string $reason): string
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw new LogicException('A reason is required for this order transition.');
        }

        return $reason;
    }
}
