<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\LedgerAccountId;

interface BalanceProjectionRepository
{
    public function get(LedgerAccountId $accountId, FinancialAssetId $assetId): ?BalanceSnapshot;

    public function save(BalanceSnapshot $snapshot): void;
}
