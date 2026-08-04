<?php

namespace App\Domain\Financial\Balance;

use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\LedgerAccountId;

final readonly class BalanceSnapshot
{
    public function __construct(
        private LedgerAccountId $accountId,
        private FinancialAssetId $assetId,
        private ExactDecimal $posted,
        private ExactDecimal $reserved,
    ) {}

    public function accountId(): LedgerAccountId
    {
        return $this->accountId;
    }

    public function assetId(): FinancialAssetId
    {
        return $this->assetId;
    }

    public function posted(): ExactDecimal
    {
        return $this->posted;
    }

    public function reserved(): ExactDecimal
    {
        return $this->reserved;
    }

    public function available(): ExactDecimal
    {
        return $this->posted->subtract($this->reserved);
    }

    public function withPosted(ExactDecimal $posted): self
    {
        return new self($this->accountId, $this->assetId, $posted, $this->reserved);
    }

    public function withReserved(ExactDecimal $reserved): self
    {
        return new self($this->accountId, $this->assetId, $this->posted, $reserved);
    }
}
