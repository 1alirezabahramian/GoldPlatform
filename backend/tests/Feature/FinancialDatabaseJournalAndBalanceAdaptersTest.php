<?php

namespace Tests\Feature;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\Contracts\TenantScopedBalanceProjectionRepository;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\Enums\JournalStatus;
use App\Domain\Financial\Journal\Journal;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Journal\JournalLine;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FinancialDatabaseJournalAndBalanceAdaptersTest extends TestCase
{
    use RefreshDatabase;

    public function test_posted_journal_round_trips_through_database_with_tenant_isolation(): void
    {
        $repository = $this->app->make(TenantScopedJournalRepository::class);
        $scopeA = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $scopeB = new FinancialScope('tenant-b', 'company-a', 'branch-a');
        $traceId = TraceId::generate();
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'rial');

        $journal = new Journal(
            traceId: $traceId,
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('journal-db-roundtrip'),
            lines: [
                new JournalLine(
                    LedgerAccountId::fromString('customer:1:money'),
                    $asset,
                    JournalSide::DEBIT,
                    ExactDecimal::fromString('12345678901234567890.123456789'),
                ),
                new JournalLine(
                    LedgerAccountId::fromString('system:clearing:money'),
                    $asset,
                    JournalSide::CREDIT,
                    ExactDecimal::fromString('12345678901234567890.123456789'),
                ),
            ],
            description: 'database round trip',
        );

        $repository->save($scopeA, JournalDocument::draft($journal)->post());

        $restored = $repository->findByTraceId($scopeA, $traceId);

        self::assertNotNull($restored);
        self::assertSame(JournalStatus::POSTED, $restored->status());
        self::assertSame('database round trip', $restored->journal()->description());
        self::assertSame(
            '12345678901234567890.123456789',
            $restored->journal()->lines()[0]->amount()->value(),
        );
        self::assertNull($repository->findByTraceId($scopeB, $traceId));
    }

    public function test_balance_snapshot_round_trips_and_remains_tenant_scoped(): void
    {
        $repository = $this->app->make(TenantScopedBalanceProjectionRepository::class);
        $scopeA = new FinancialScope('tenant-a');
        $scopeB = new FinancialScope('tenant-b');
        $account = LedgerAccountId::fromString('customer:1:gold');
        $asset = new FinancialAssetId(FinancialAssetType::GOLD, '18k');

        $repository->save($scopeA, new BalanceSnapshot(
            accountId: $account,
            assetId: $asset,
            posted: ExactDecimal::fromString('-10.125'),
            reserved: ExactDecimal::fromString('2.500'),
        ));

        $restored = $repository->get($scopeA, $account, $asset);

        self::assertNotNull($restored);
        self::assertSame('-10.125', $restored->posted()->value());
        self::assertSame('2.5', $restored->reserved()->value());
        self::assertSame('-12.625', $restored->available()->value());
        self::assertNull($repository->get($scopeB, $account, $asset));
    }
}
