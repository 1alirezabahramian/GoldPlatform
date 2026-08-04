<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Contracts\TenantScopedJournalProjectionApplier;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\Journal\Journal;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Journal\JournalLine;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Financial\Posting\TenantScopedAtomicJournalPostingService;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TenantScopedAtomicJournalPostingServiceTest extends TestCase
{
    #[Test]
    public function identical_idempotency_keys_use_different_locks_for_different_tenants(): void
    {
        $lockKeys = [];
        $concurrency = $this->createMock(ConcurrencyGuard::class);
        $concurrency->method('synchronized')->willReturnCallback(
            function (string $resource, callable $operation) use (&$lockKeys): mixed {
                $lockKeys[] = $resource;
                return $operation();
            }
        );

        $atomic = $this->createMock(AtomicFinancialOperation::class);
        $atomic->method('execute')->willReturnCallback(static fn (callable $operation): mixed => $operation());

        $idempotency = $this->createMock(TenantScopedIdempotencyRegistry::class);
        $idempotency->method('find')->willReturn(null);

        $service = new TenantScopedAtomicJournalPostingService(
            $idempotency,
            $concurrency,
            $atomic,
            $this->createMock(TenantScopedJournalRepository::class),
            $this->createMock(TenantScopedFinancialEventStore::class),
            $this->createMock(TenantScopedJournalProjectionApplier::class),
        );

        $service->post(new FinancialScope('tenant-a'), $this->draft(), 'hash-a');
        $service->post(new FinancialScope('tenant-b'), $this->draft(), 'hash-a');

        self::assertCount(2, array_unique($lockKeys));
        self::assertStringContainsString('tenant:tenant-a', $lockKeys[0]);
        self::assertStringContainsString('tenant:tenant-b', $lockKeys[1]);
    }

    private function draft(): JournalDocument
    {
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');
        $journal = new Journal(
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('same-key'),
            lines: [
                new JournalLine(LedgerAccountId::fromString('a'), $asset, JournalSide::DEBIT, ExactDecimal::fromString('10')),
                new JournalLine(LedgerAccountId::fromString('b'), $asset, JournalSide::CREDIT, ExactDecimal::fromString('10')),
            ],
        );

        return JournalDocument::draft($journal);
    }
}
