<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\Contracts\TenantScopedBalanceProjectionRepository;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\LedgerAccountId;

final class InMemoryTenantScopedBalanceProjectionRepository implements TenantScopedBalanceProjectionRepository
{
    /** @var array<string, BalanceSnapshot> */
    private array $snapshots = [];

    public function get(
        FinancialScope $scope,
        LedgerAccountId $accountId,
        FinancialAssetId $assetId,
    ): ?BalanceSnapshot {
        return $this->snapshots[$this->key($scope, $accountId, $assetId)] ?? null;
    }

    public function save(FinancialScope $scope, BalanceSnapshot $snapshot): void
    {
        $this->snapshots[$this->key($scope, $snapshot->accountId(), $snapshot->assetId())] = $snapshot;
    }

    private function key(
        FinancialScope $scope,
        LedgerAccountId $accountId,
        FinancialAssetId $assetId,
    ): string {
        return $scope->key().'|account|'.$accountId->value().'|asset|'.(string) $assetId;
    }
}
