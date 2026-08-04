<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\LedgerAccountId;

interface TenantScopedBalanceProjectionRepository
{
    public function get(
        FinancialScope $scope,
        LedgerAccountId $accountId,
        FinancialAssetId $assetId,
    ): ?BalanceSnapshot;

    public function save(FinancialScope $scope, BalanceSnapshot $snapshot): void;
}
