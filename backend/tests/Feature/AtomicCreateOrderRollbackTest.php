<?php

namespace Tests\Feature;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Domain\Trading\Enums\QuoteStatus;
use App\Domain\Trading\Order\CreateOrderFromQuoteCommand;
use App\Domain\Trading\Order\IdempotentCreateOrderFromQuoteService;
use App\Domain\Trading\Quote\Quote;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

final class AtomicCreateOrderRollbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_failure_rolls_back_quote_order_and_idempotency(): void
    {
        $scope = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $requestedAt = new DateTimeImmutable('2026-08-05T00:00:00+00:00');
        $key = IdempotencyKey::fromString('rollback-create-order');
        $quote = Quote::request(
            scope: $scope,
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('rollback-quote'),
            requestedAt: $requestedAt,
        )->freeze($requestedAt->modify('+5 minutes'));

        $quotes = $this->app->make(TenantScopedQuoteRepository::class);
        $orders = $this->app->make(TenantScopedOrderRepository::class);
        $idempotency = $this->app->make(TenantScopedIdempotencyRegistry::class);
        $quotes->save($scope, $quote);

        $this->app->instance(TenantScopedFinancialEventStore::class, new class implements TenantScopedFinancialEventStore {
            public function append(FinancialScope $scope, FinancialEvent $event): void
            {
                throw new RuntimeException('Simulated event persistence failure.');
            }

            public function byCorrelationId(FinancialScope $scope, CorrelationId $correlationId): array
            {
                return [];
            }
        });

        $command = new CreateOrderFromQuoteCommand(
            scope: $scope,
            quoteId: $quote->id(),
            traceId: TraceId::generate(),
            idempotencyKey: $key,
            requestHash: 'rollback-request-hash',
            usedAt: $requestedAt->modify('+1 minute'),
            createdAt: $requestedAt->modify('+1 minute'),
        );

        try {
            $this->app->make(IdempotentCreateOrderFromQuoteService::class)->execute($command);
            self::fail('Expected event persistence failure was not thrown.');
        } catch (RuntimeException $exception) {
            self::assertSame('Simulated event persistence failure.', $exception->getMessage());
        }

        self::assertSame(QuoteStatus::FROZEN, $quotes->find($scope, $quote->id())?->status());
        self::assertNull($orders->findByQuote($scope, $quote->id()));
        self::assertNull($idempotency->find($scope, $key));
    }
}
