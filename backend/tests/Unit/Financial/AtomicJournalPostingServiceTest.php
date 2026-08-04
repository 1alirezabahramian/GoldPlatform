<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Contracts\JournalProjectionApplier;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\Enums\JournalStatus;
use App\Domain\Financial\Journal\Journal;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Journal\JournalLine;
use App\Domain\Financial\Posting\AtomicJournalPostingService;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Infrastructure\Financial\InMemory\InMemoryAtomicFinancialOperation;
use App\Infrastructure\Financial\InMemory\InMemoryConcurrencyGuard;
use App\Infrastructure\Financial\InMemory\InMemoryFinancialEventStore;
use App\Infrastructure\Financial\InMemory\InMemoryIdempotencyRegistry;
use App\Infrastructure\Financial\InMemory\InMemoryJournalRepository;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AtomicJournalPostingServiceTest extends TestCase
{
    #[Test]
    public function it_posts_once_and_replays_the_saved_result_for_the_same_request(): void
    {
        $journals = new InMemoryJournalRepository();
        $events = new InMemoryFinancialEventStore();
        $projection = new class implements JournalProjectionApplier {
            public int $calls = 0;
            public function apply(JournalDocument $postedJournal): void { $this->calls++; }
        };

        $service = new AtomicJournalPostingService(
            new InMemoryIdempotencyRegistry(),
            new InMemoryConcurrencyGuard(),
            new InMemoryAtomicFinancialOperation(),
            $journals,
            $events,
            $projection,
        );

        $draft = $this->draft('journal:post:1');
        $first = $service->post($draft, 'hash-1');
        $replayed = $service->post($draft, 'hash-1');

        self::assertSame(JournalStatus::POSTED, $first->status());
        self::assertSame($first, $replayed);
        self::assertSame(1, $projection->calls);
        self::assertCount(1, $events->byCorrelationId($draft->journal()->correlationId()));
    }

    #[Test]
    public function it_rejects_reusing_the_key_for_a_different_request(): void
    {
        $service = new AtomicJournalPostingService(
            new InMemoryIdempotencyRegistry(),
            new InMemoryConcurrencyGuard(),
            new InMemoryAtomicFinancialOperation(),
            new InMemoryJournalRepository(),
            new InMemoryFinancialEventStore(),
            new class implements JournalProjectionApplier {
                public function apply(JournalDocument $postedJournal): void {}
            },
        );

        $draft = $this->draft('journal:post:conflict');
        $service->post($draft, 'hash-a');

        $this->expectException(DomainException::class);
        $service->post($draft, 'hash-b');
    }

    private function draft(string $idempotencyKey): JournalDocument
    {
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');
        $journal = new Journal(
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString($idempotencyKey),
            lines: [
                new JournalLine(
                    LedgerAccountId::fromString('customer:1'),
                    $asset,
                    JournalSide::DEBIT,
                    ExactDecimal::fromString('100'),
                ),
                new JournalLine(
                    LedgerAccountId::fromString('platform:clearing'),
                    $asset,
                    JournalSide::CREDIT,
                    ExactDecimal::fromString('100'),
                ),
            ],
        );

        return JournalDocument::draft($journal);
    }
}
