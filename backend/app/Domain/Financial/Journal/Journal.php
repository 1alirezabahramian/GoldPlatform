<?php

namespace App\Domain\Financial\Journal;

use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use InvalidArgumentException;

final readonly class Journal
{
    /** @var list<JournalLine> */
    private array $lines;

    /**
     * @param list<JournalLine> $lines
     */
    public function __construct(
        private TraceId $traceId,
        private CorrelationId $correlationId,
        private IdempotencyKey $idempotencyKey,
        array $lines,
        private ?string $description = null,
    ) {
        if (count($lines) < 2) {
            throw new InvalidArgumentException('A journal requires at least two lines.');
        }

        foreach ($lines as $line) {
            if (! $line instanceof JournalLine) {
                throw new InvalidArgumentException('Journal lines must be JournalLine instances.');
            }
        }

        $this->assertBalanced($lines);
        $this->lines = array_values($lines);
    }

    public function traceId(): TraceId
    {
        return $this->traceId;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }

    public function idempotencyKey(): IdempotencyKey
    {
        return $this->idempotencyKey;
    }

    /** @return list<JournalLine> */
    public function lines(): array
    {
        return $this->lines;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function reversed(
        TraceId $traceId,
        IdempotencyKey $idempotencyKey,
    ): self {
        return new self(
            traceId: $traceId,
            correlationId: $this->correlationId,
            idempotencyKey: $idempotencyKey,
            lines: array_map(
                static fn (JournalLine $line): JournalLine => $line->reversed(),
                $this->lines,
            ),
            description: $this->description,
        );
    }

    /**
     * Balance is checked independently for every exact financial asset identity.
     * This supports multi-asset journals without mixing Money, Gold, Coin or Currency totals.
     *
     * @param list<JournalLine> $lines
     */
    private function assertBalanced(array $lines): void
    {
        /** @var array<string, array{debit: ExactDecimal, credit: ExactDecimal}> $totals */
        $totals = [];

        foreach ($lines as $line) {
            $assetKey = (string) $line->assetId();
            $totals[$assetKey] ??= [
                'debit' => ExactDecimal::fromString('0'),
                'credit' => ExactDecimal::fromString('0'),
            ];

            $side = $line->side() === JournalSide::DEBIT ? 'debit' : 'credit';
            $totals[$assetKey][$side] = $totals[$assetKey][$side]->add($line->amount());
        }

        foreach ($totals as $assetKey => $total) {
            if (! $total['debit']->equals($total['credit'])) {
                throw new InvalidArgumentException(
                    "Journal is not balanced for financial asset {$assetKey}."
                );
            }
        }
    }
}
