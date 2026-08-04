<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\Enums\JournalStatus;
use App\Domain\Financial\Journal\Journal;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Journal\JournalLine;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JournalLifecycleTest extends TestCase
{
    #[Test]
    public function a_draft_can_be_posted_without_mutating_the_original(): void
    {
        $draft = JournalDocument::draft($this->journal());
        $posted = $draft->post();

        self::assertSame(JournalStatus::DRAFT, $draft->status());
        self::assertSame(JournalStatus::POSTED, $posted->status());
        self::assertSame($draft->journal(), $posted->journal());
    }

    #[Test]
    public function only_a_draft_can_be_posted(): void
    {
        $posted = JournalDocument::draft($this->journal())->post();

        $this->expectException(DomainException::class);

        $posted->post();
    }

    #[Test]
    public function only_a_posted_journal_can_be_reversed(): void
    {
        $draft = JournalDocument::draft($this->journal());

        $this->expectException(DomainException::class);

        $draft->reverse(
            TraceId::generate(),
            IdempotencyKey::fromString('journal:lifecycle:invalid-reversal'),
        );
    }

    #[Test]
    public function reversal_marks_the_original_and_creates_a_new_posted_journal(): void
    {
        $posted = JournalDocument::draft($this->journal())->post();
        $reversalTrace = TraceId::generate();

        $result = $posted->reverse(
            $reversalTrace,
            IdempotencyKey::fromString('journal:lifecycle:reversal'),
        );

        self::assertSame(JournalStatus::REVERSED, $result->original()->status());
        self::assertTrue($reversalTrace->equals($result->original()->reversalTraceId()));
        self::assertSame(JournalStatus::POSTED, $result->reversal()->status());
        self::assertNotSame($posted->journal(), $result->reversal()->journal());
        self::assertTrue(
            $posted->journal()->correlationId()->equals(
                $result->reversal()->journal()->correlationId()
            )
        );
        self::assertSame(
            JournalSide::CREDIT,
            $result->reversal()->journal()->lines()[0]->side()
        );
    }

    private function journal(): Journal
    {
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');

        return new Journal(
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('journal:lifecycle:create'),
            lines: [
                new JournalLine(
                    accountId: LedgerAccountId::fromString('customer:1'),
                    assetId: $asset,
                    side: JournalSide::DEBIT,
                    amount: ExactDecimal::fromString('100'),
                ),
                new JournalLine(
                    accountId: LedgerAccountId::fromString('platform:clearing'),
                    assetId: $asset,
                    side: JournalSide::CREDIT,
                    amount: ExactDecimal::fromString('100'),
                ),
            ],
        );
    }
}
