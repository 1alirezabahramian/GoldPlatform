<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Domain\Trading\Enums\OrderDecision;
use App\Domain\Trading\Validation\OrderValidationContext;
use App\Domain\Trading\Validation\OrderValidationEngine;
use App\Domain\Trading\ValueObjects\OrderId;
use DomainException;

final readonly class IdempotentOrderDecisionService
{
    public function __construct(
        private TenantScopedOrderRepository $orders,
        private TenantScopedQuoteRepository $quotes,
        private OrderValidationEngine $validation,
        private TenantScopedIdempotencyRegistry $idempotency,
        private ConcurrencyGuard $concurrency,
        private AtomicFinancialOperation $atomic,
    ) {}

    public function decide(OrderDecisionCommand $command): Order
    {
        $scope = $command->scope();
        $key = $command->idempotencyKey();
        $operation = 'trading.order.'.$command->decision()->value;
        $lockKey = 'trading:'.$scope->key().':order-decision:'.$key->value();

        return $this->concurrency->synchronized($lockKey, function () use ($command, $scope, $key, $operation): Order {
            $existing = $this->idempotency->find($scope, $key);

            if ($existing !== null) {
                if (! $existing->matches($operation, $command->requestHash())) {
                    throw new DomainException('Idempotency key was already used for a different order decision request.');
                }

                $reference = $existing->resultReference();
                if ($reference === null) {
                    throw new DomainException('Order decision idempotency record has no result reference.');
                }

                $saved = $this->orders->find($scope, OrderId::fromString($reference));
                if ($saved === null) {
                    throw new DomainException('Idempotent order decision result cannot be resolved.');
                }

                return $saved;
            }

            return $this->atomic->execute(function () use ($command, $scope, $key, $operation): Order {
                $order = $this->orders->find($scope, $command->orderId());
                if ($order === null) {
                    throw new DomainException('Order was not found in the requested financial scope.');
                }

                $quote = $this->quotes->find($scope, $order->quoteId());
                if ($quote === null) {
                    throw new DomainException('Order quote was not found in the requested financial scope.');
                }

                $result = $this->validation->validate(new OrderValidationContext($scope, $order, $quote));
                if (! $result->isValid()) {
                    $codes = array_map(static fn ($failure): string => $failure->code(), $result->failures());
                    throw new DomainException('Order validation failed: '.implode(', ', $codes));
                }

                $decided = $command->decision() === OrderDecision::APPROVE
                    ? $order->approve()
                    : $order->reject((string) $command->rejectionReason());

                $this->orders->save($scope, $decided);
                $this->idempotency->claim($scope, new IdempotencyRecord(
                    key: $key,
                    operation: $operation,
                    requestHash: $command->requestHash(),
                    traceId: $command->traceId(),
                    resultReference: $decided->id()->value(),
                ));

                return $decided;
            });
        });
    }
}
