<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\Journal\Journal;
use App\Domain\Financial\Journal\JournalLine;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JournalTest extends TestCase
{
    #[Test]
    public function it_accepts_a_balanced_journal_for_one_exact_asset(): void
    {
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');

        $journal = $this->journal([
            $this->line('customer:1', $asset, JournalSide::DEBIT, '100'),
            $this->line('platform:clearing', $asset, JournalSide::CREDIT, '100'),
        ]);

        self::assertCount(2, $journal->lines());
        self::assertSame('money:toman', (string) $journal->lines()[0]->assetId());
    }

    #[Test]
    public function it_rejects_an_unbalanced_journal(): void
    {
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Journal is not balanced');

        $this->journal([
            $this->line('customer:1', $asset, JournalSide::DEBIT, '100'),
            $this->line('platform:clearing', $asset, JournalSide::CREDIT, '99'),
        ]);
    }

    #[Test]
    public function it_balances_each_asset_identity_independently(): void
    {
        $money = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');
        $gold = new FinancialAssetId(FinancialAssetType::GOLD, '750');

        $journal = $this->journal([
            $this->line('customer:money', $money, JournalSide::DEBIT, '1000'),
            $this->line('platform:money', $money, JournalSide::CREDIT, '1000'),
            $this->line('platform:gold', $gold, JournalSide::DEBIT, '1.25'),
            $this->line('customer:gold', $gold, JournalSide::CREDIT, '1.25'),
        ]);

        self::assertCount(4, $journal->lines());
    }

    #[Test]
    public function it_does_not_allow_one_asset_to_balance_another_asset(): void
    {
        $money = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');
        $gold = new FinancialAssetId(FinancialAssetType::GOLD, '750');

        $this->expectException(InvalidArgumentException::class);

        $this->journal([
            $this->line('customer:money', $money, JournalSide::DEBIT, '1000'),
            $this->line('customer:gold', $gold, JournalSide::CREDIT, '1000'),
        ]);
    }

    #[Test]
    public function journal_lines_require_a_positive_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->line(
            'customer:1',
            new FinancialAssetId(FinancialAssetType::MONEY, 'toman'),
            JournalSide::DEBIT,
            '0',
        );
    }

    #[Test]
    public function reversal_flips_all_sides_and_preserves_correlation(): void
    {
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');
        $journal = $this->journal([
            $this->line('customer:1', $asset, JournalSide::DEBIT, '100'),
            $this->line('platform:clearing', $asset, JournalSide::CREDIT, '100'),
        ]);

        $reversal = $journal->reversed(
            TraceId::generate(),
            IdempotencyKey::fromString('journal:test:reverse'),
        );

        self::assertTrue($journal->correlationId()->equals($reversal->correlationId()));
        self::assertSame(JournalSide::CREDIT, $reversal->lines()[0]->side());
        self::assertSame(JournalSide::DEBIT, $reversal->lines()[1]->side());
        self::assertSame('100', $reversal->lines()[0]->amount()->value());
    }

    /** @param list<JournalLine> $lines */
    private function journal(array $lines): Journal
    {
        return new Journal(
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('journal:test:create'),
            lines: $lines,
        );
    }

    private function line(
        string $account,
        FinancialAssetId $asset,
        JournalSide $side,
        string $amount,
    ): JournalLine {
        return new JournalLine(
            accountId: LedgerAccountId::fromString($account),
            assetId: $asset,
            side: $side,
            amount: ExactDecimal::fromString($amount),
        );
    }
}
