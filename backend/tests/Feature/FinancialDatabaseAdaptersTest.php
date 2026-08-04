<?php

namespace Tests\Feature;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FinancialDatabaseAdaptersTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_store_persists_and_isolates_events_by_financial_scope(): void
    {
        $store = app(TenantScopedFinancialEventStore::class);
        $tenantA = new FinancialScope('tenant-a');
        $tenantB = new FinancialScope('tenant-b');
        $correlationId = CorrelationId::generate();

        $store->append($tenantA, new FinancialEvent(
            name: 'financial.test.event',
            traceId: TraceId::generate(),
            correlationId: $correlationId,
            idempotencyKey: IdempotencyKey::fromString('event-key'),
            occurredAt: new DateTimeImmutable('2026-08-04T21:00:00+03:30'),
            payload: ['source' => 'database-test'],
        ));

        self::assertCount(1, $store->byCorrelationId($tenantA, $correlationId));
        self::assertCount(0, $store->byCorrelationId($tenantB, $correlationId));
    }

    public function test_idempotency_registry_replays_same_request_and_isolates_tenants(): void
    {
        $registry = app(TenantScopedIdempotencyRegistry::class);
        $tenantA = new FinancialScope('tenant-a');
        $tenantB = new FinancialScope('tenant-b');
        $key = IdempotencyKey::fromString('shared-key');
        $record = new IdempotencyRecord(
            key: $key,
            operation: 'financial.journal.post',
            requestHash: hash('sha256', 'request-a'),
            traceId: TraceId::generate(),
            resultReference: 'result-a',
        );

        $registry->claim($tenantA, $record);
        $registry->claim($tenantA, $record);

        self::assertNotNull($registry->find($tenantA, $key));
        self::assertNull($registry->find($tenantB, $key));
    }
}
