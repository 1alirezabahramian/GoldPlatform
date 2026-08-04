<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Domain\Trading\ValueObjects\OrderId;
use DomainException;

final readonly class IdempotentCreateOrderFromQuoteService
{
    private const OPERATION = 'trading.order.create-from-quote';

    public function __construct(
        private TenantScopedQuoteRepository $quotes,
        private TenantScopedOrderRepository $orders,
        private TenantScopedIdempotencyRegistry $idempotency,
        private TenantScopedFinancialEventStore $events,
        private ConcurrencyGuard $concurrency,
        private AtomicFinancialOperation $atomic,
    ) {}

    public function execute(CreateOrderFromQuoteCommand $command): Order
    {
        $scope = $command->scope();
        $lockKey = 'trading:'.$scope->key().':quote:'.$command->quoteId()->value().':create-order';

        return $this->concurrency->synchronized($lockKey, function () use ($command, $scope): Order {
            $existing = $this->idempotency->find($scope, $command->idempotencyKey());

            if ($existing !== null) {
                if (! $existing->matches(self::OPERATION, $command->requestHash())) {
                    throw new DomainException('Idempotency key was already used for a different order creation request.');
                }

                $reference = $existing->resultReference();
                if ($reference === null) {
                    throw new DomainException('Order creation idempotency record has no result reference.');
                }

                $order = $this->orders->find($scope, OrderId::fromString($reference));
                if ($order === null) {
                    throw new DomainException('Idempotent order creation result cannot be resolved.');
                }

                return $order;
            }

            return $this->atomic->execute(function () use ($command, $scope): Order {
                if ($this->orders->findByQuote($scope, $command->quoteId()) !== null) {
                    throw new DomainException('An order already exists for this quote.');
                }

                $quote = $this->quotes->find($scope, $command->quoteId());
                if ($quote === null) {
                    throw new DomainException('Quote was not found in the requested financial scope.');
                }

                $usedQuote = $quote->use($command->usedAt());
                $order = Order::draftFromUsedQuote(
                    quote: $usedQuote,
                    traceId: $command->traceId(),
                    idempotencyKey: $command->idempotencyKey(),
                    createdAt: $command->createdAt(),
                );

                $this->quotes->save($scope, $usedQuote);
                $this->orders->save($scope, $order);
                $this->events->append($scope, new FinancialEvent(
                    name: 'trading.order.created-from-quote',
                    traceId: $command->traceId(),
                    correlationId: $usedQuote->correlationId(),
                    idempotencyKey: $command->idempotencyKey(),
                    occurredAt: $command->createdAt(),
                    payload: [
                        'quote_id' => $usedQuote->id()->value(),
                        'order_id' => $order->id()->value(),
                    ],
                ));
                $this->idempotency->claim($scope, new IdempotencyRecord(
                    key: $command->idempotencyKey(),
                    operation: self::OPERATION,
                    requestHash: $command->requestHash(),
                    traceId: $command->traceId(),
                    resultReference: $order->id()->value(),
                ));

                return $order;
            });
        });
    }
}
