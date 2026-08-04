<?php

namespace App\Domain\Financial\Journal;

use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use InvalidArgumentException;

final readonly class JournalLine
{
    public function __construct(
        private LedgerAccountId $accountId,
        private FinancialAssetId $assetId,
        private JournalSide $side,
        private ExactDecimal $amount,
        private ?string $description = null,
    ) {
        if ($amount->compare(ExactDecimal::fromString('0')) <= 0) {
            throw new InvalidArgumentException('Journal line amount must be greater than zero.');
        }
    }

    public function accountId(): LedgerAccountId
    {
        return $this->accountId;
    }

    public function assetId(): FinancialAssetId
    {
        return $this->assetId;
    }

    public function side(): JournalSide
    {
        return $this->side;
    }

    public function amount(): ExactDecimal
    {
        return $this->amount;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function reversed(): self
    {
        return new self(
            accountId: $this->accountId,
            assetId: $this->assetId,
            side: $this->side->opposite(),
            amount: $this->amount,
            description: $this->description,
        );
    }
}
