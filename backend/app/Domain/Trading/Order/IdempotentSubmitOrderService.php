<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\ValueObjects\OrderId;
use DomainException;

final readonly class IdempotentSubmitOrderService
{
    public function __construct(
        private TenantScopedOrderRepository $orders,
        private TenantScopedIdempotencyRegistry $idempotency,
        private TenantScopedFinancialEventStore $events,
        private ConcurrencyGuard $concurrency,
        private AtomicFinancialOperation $atomic,
    ) {}

    public function execute(SubmitOrderCommand $command): Order
    {
        $scope = $command->scope();
        $key = $command->idempotencyKey();
        $operation = 'trading.order.submit';
        $lockKey = $scope->key().':order:'.$command->orderId()->value().':submit';

        return $this->concurrency->synchronized($lockKey, function () use ($command, $scope, $key, $operation): Order {
            $existing = $this->idempotency->find($scope, $key);

            if ($existing !== null) {
                if (! $existing->matches($operation, $command->requestHash())) {
                    throw new DomainException('Idempotency key was already used for a different order submission request.');
                }

                $reference = $existing->resultReference();
                if ($reference === null) {
                    throw new DomainException('Order submission idempotency record has no result reference.');
                }

                $saved = $this->orders->find($scope, OrderId::fromString($reference));
                if ($saved === null) {
                    throw new DomainException('Idempotent order submission result cannot be resolved.');
                }

                return $saved;
            }

            return $this->atomic->execute(function () use ($command, $scope, $key, $operation): Order {
                $order = $this->orders->find($scope, $command->orderId());
                if ($order === null) {
                    throw new DomainException('Order was not found in the requested financial scope.');
                }

                $submitted = $order->submit($command->submittedAt());
                $this->orders->save($scope, $submitted);
                $this->events->append($scope, new FinancialEvent(
                    name: 'trading.order.submitted',
                    traceId: $command->traceId(),
                    correlationId: $submitted->correlationId(),
                    idempotencyKey: $key,
                    occurredAt: $command->submittedAt(),
                    payload: [
                        'order_id' => $submitted->id()->value(),
                        'quote_id' => $submitted->quoteId()->value(),
                    ],
                ));
                $this->idempotency->claim($scope, new IdempotencyRecord(
                    key: $key,
                    operation: $operation,
                    requestHash: $command->requestHash(),
                    traceId: $command->traceId(),
                    resultReference: $submitted->id()->value(),
                ));

                return $submitted;
            });
        });
    }
}
