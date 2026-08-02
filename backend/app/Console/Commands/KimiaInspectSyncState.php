<?php

namespace App\Console\Commands;

use App\Models\AccountGroup;
use App\Models\ExternalAccount;
use App\Models\KimiaCoin;
use App\Models\KimiaCurrency;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class KimiaInspectSyncState extends Command
{
    protected $signature = 'kimia:inspect-sync-state
                            {--account= : Verify one Kimia AccountId without exposing personal fields}';

    protected $description = 'Inspect local Kimia projections without exposing customer identity data';

    public function handle(): int
    {
        $this->table(
            ['Projection', 'Rows', 'Last synchronized'],
            [
                $this->summaryRow(
                    'External accounts',
                    ExternalAccount::query()->where('provider', 'kimia'),
                    'last_synced_at'
                ),
                $this->summaryRow(
                    'Account groups',
                    AccountGroup::query(),
                    'synced_at'
                ),
                $this->summaryRow(
                    'Coins',
                    KimiaCoin::query(),
                    'synced_at'
                ),
                $this->summaryRow(
                    'Currencies',
                    KimiaCurrency::query(),
                    'synced_at'
                ),
            ]
        );

        $requestedAccount = $this->option('account');

        if ($requestedAccount === null || $requestedAccount === '') {
            return self::SUCCESS;
        }

        $accountId = filter_var(
            $requestedAccount,
            FILTER_VALIDATE_INT
        );

        if ($accountId === false || $accountId <= 0) {
            $this->error('Account ID must be a positive integer.');

            return self::FAILURE;
        }

        $account = ExternalAccount::query()
            ->where('provider', 'kimia')
            ->where('external_id', $accountId)
            ->first();

        if ($account === null) {
            $this->error(
                "Kimia AccountId {$accountId} is missing from the local projection."
            );

            return self::FAILURE;
        }

        $this->table(
            ['AccountId', 'Type', 'Active', 'Sync status', 'Last synchronized'],
            [[
                $account->external_id,
                $account->type,
                $account->is_active ? 'yes' : 'no',
                $account->sync_status,
                $account->last_synced_at?->toDateTimeString(),
            ]]
        );

        $this->line('Customer name, mobile, national code, and raw payload are omitted.');

        return self::SUCCESS;
    }

    /**
     * @return array{string, int, mixed}
     */
    private function summaryRow(
        string $label,
        Builder $query,
        string $timestampColumn
    ): array {
        return [
            $label,
            (clone $query)->count(),
            (clone $query)->max($timestampColumn) ?? '-',
        ];
    }
}
