<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use DomainException;

final readonly class IdempotentOrderTerminalService
{
    public function __construct(
        private TenantScopedOrderRepository $orders,
        private TenantScopedIdempotencyRegistry $idempotency,
        private ConcurrencyGuard $concurrency,
        private AtomicFinancialOperation $atomic,
    ) {}

    public function execute(OrderTerminalCommand $command): Order
    {
        $operation = 'trading.order.'.$command->action()->value;
        $lockKey = $command->scope()->key().':order:'.$command->orderId()->value().':'.$command->action()->value;

        return $this->concurrency->synchronized($lockKey, function () use ($command, $operation): Order {
            $existing = $this->idempotency->find($command->scope(), $command->idempotencyKey());

            if ($existing !== null) {
                if (! $existing->matches($operation, $command->requestHash())) {
                    throw new DomainException('Idempotency key was already used for a different terminal order command.');
                }

                $order = $this->orders->find($command->scope(), $command->orderId());
                if ($order === null) {
                    throw new DomainException('Idempotent terminal order result cannot be resolved.');
                }

                return $order;
            }

            return $this->atomic->execute(function () use ($command, $operation): Order {
                $order = $this->orders->find($command->scope(), $command->orderId());
                if ($order === null) {
                    throw new DomainException('Order was not found in the requested financial scope.');
                }

                $updated = match ($command->action()) {
                    OrderTerminalAction::EXPIRE => $order->expire(),
                    OrderTerminalAction::CANCEL => $order->cancel(),
                };

                $this->orders->save($command->scope(), $updated);
                $this->idempotency->claim($command->scope(), new IdempotencyRecord(
                    key: $command->idempotencyKey(),
                    operation: $operation,
                    requestHash: $command->requestHash(),
                    traceId: $order->traceId(),
                    resultReference: $order->id()->value(),
                ));

                return $updated;
            });
        });
    }
}
