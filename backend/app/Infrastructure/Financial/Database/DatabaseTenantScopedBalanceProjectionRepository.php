<?php

namespace App\Infrastructure\Financial\Database;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\Contracts\TenantScopedBalanceProjectionRepository;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use Illuminate\Support\Facades\DB;

final class DatabaseTenantScopedBalanceProjectionRepository implements TenantScopedBalanceProjectionRepository
{
    public function get(
        FinancialScope $scope,
        LedgerAccountId $accountId,
        FinancialAssetId $assetId,
    ): ?BalanceSnapshot {
        $row = DB::table('financial_balance_projections')
            ->where('scope_hash', hash('sha256', $scope->key()))
            ->where('ledger_account_id', $accountId->value())
            ->where('asset_type', $assetId->type()->value)
            ->where('asset_id', $assetId->externalId())
            ->first();

        if ($row === null) {
            return null;
        }

        return new BalanceSnapshot(
            accountId: $accountId,
            assetId: $assetId,
            posted: ExactDecimal::fromString($row->posted_amount),
            reserved: ExactDecimal::fromString($row->reserved_amount),
        );
    }

    public function save(FinancialScope $scope, BalanceSnapshot $snapshot): void
    {
        $scopeKey = $scope->key();
        $scopeHash = hash('sha256', $scopeKey);
        $now = now();

        $existing = DB::table('financial_balance_projections')
            ->where('scope_hash', $scopeHash)
            ->where('ledger_account_id', $snapshot->accountId()->value())
            ->where('asset_type', $snapshot->assetId()->type()->value)
            ->where('asset_id', $snapshot->assetId()->externalId())
            ->first();

        $values = [
            'scope_key' => $scopeKey,
            'scope_hash' => $scopeHash,
            'tenant_id' => $scope->tenantId(),
            'company_id' => $scope->companyId(),
            'branch_id' => $scope->branchId(),
            'ledger_account_id' => $snapshot->accountId()->value(),
            'asset_type' => $snapshot->assetId()->type()->value,
            'asset_id' => $snapshot->assetId()->externalId(),
            'posted_amount' => $snapshot->posted()->value(),
            'reserved_amount' => $snapshot->reserved()->value(),
            'version' => $existing === null ? 0 : ((int) $existing->version + 1),
            'updated_at' => $now,
        ];

        if ($existing === null) {
            DB::table('financial_balance_projections')->insert([
                ...$values,
                'created_at' => $now,
            ]);

            return;
        }

        DB::table('financial_balance_projections')
            ->where('id', $existing->id)
            ->update($values);
    }
}
