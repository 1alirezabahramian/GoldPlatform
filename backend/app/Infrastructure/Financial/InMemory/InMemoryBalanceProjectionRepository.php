<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\Contracts\BalanceProjectionRepository;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\LedgerAccountId;

final class InMemoryBalanceProjectionRepository implements BalanceProjectionRepository
{
    /** @var array<string, BalanceSnapshot> */
    private array $snapshots = [];

    public function get(LedgerAccountId $accountId, FinancialAssetId $assetId): ?BalanceSnapshot
    {
        return $this->snapshots[$this->key($accountId, $assetId)] ?? null;
    }

    public function save(BalanceSnapshot $snapshot): void
    {
        $this->snapshots[$this->key($snapshot->accountId(), $snapshot->assetId())] = $snapshot;
    }

    private function key(LedgerAccountId $accountId, FinancialAssetId $assetId): string
    {
        return $accountId->value().'|'.(string) $assetId;
    }
}
