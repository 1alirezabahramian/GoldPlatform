<?php

namespace Tests\Feature;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\Contracts\TenantScopedBalanceProjectionRepository;
use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TenantScopedFinancialAdaptersTest extends TestCase
{
    #[Test]
    public function container_resolves_all_tenant_scoped_repository_contracts(): void
    {
        self::assertInstanceOf(
            TenantScopedJournalRepository::class,
            $this->app->make(TenantScopedJournalRepository::class),
        );
        self::assertInstanceOf(
            TenantScopedFinancialEventStore::class,
            $this->app->make(TenantScopedFinancialEventStore::class),
        );
        self::assertInstanceOf(
            TenantScopedIdempotencyRegistry::class,
            $this->app->make(TenantScopedIdempotencyRegistry::class),
        );
        self::assertInstanceOf(
            TenantScopedBalanceProjectionRepository::class,
            $this->app->make(TenantScopedBalanceProjectionRepository::class),
        );
    }

    #[Test]
    public function balance_and_events_are_isolated_between_tenants(): void
    {
        $tenantA = new FinancialScope('tenant-a', 'company-1', 'branch-1');
        $tenantB = new FinancialScope('tenant-b', 'company-1', 'branch-1');
        $account = LedgerAccountId::fromString('customer:1');
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');

        $balances = $this->app->make(TenantScopedBalanceProjectionRepository::class);
        $balances->save($tenantA, new BalanceSnapshot(
            accountId: $account,
            assetId: $asset,
            posted: ExactDecimal::fromString('100'),
            reserved: ExactDecimal::fromString('10'),
        ));

        self::assertNotNull($balances->get($tenantA, $account, $asset));
        self::assertNull($balances->get($tenantB, $account, $asset));

        $correlationId = CorrelationId::generate();
        $events = $this->app->make(TenantScopedFinancialEventStore::class);
        $events->append($tenantA, new FinancialEvent(
            name: 'financial.test',
            traceId: TraceId::generate(),
            correlationId: $correlationId,
            idempotencyKey: IdempotencyKey::fromString('tenant-test-event'),
            occurredAt: new DateTimeImmutable(),
        ));

        self::assertCount(1, $events->byCorrelationId($tenantA, $correlationId));
        self::assertSame([], $events->byCorrelationId($tenantB, $correlationId));
    }
}
