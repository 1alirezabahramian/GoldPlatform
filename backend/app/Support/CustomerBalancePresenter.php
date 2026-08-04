<?php

namespace App\Support;

use App\Models\WalletAccount;
use App\Services\Wallet\BalanceProjectionService;

final class CustomerBalancePresenter
{
    public function __construct(private readonly BalanceProjectionService $projection)
    {
    }

    /** @return array<string, mixed> */
    public function present(WalletAccount $account): array
    {
        return $this->format($account, $this->projection->snapshot($account));
    }

    /** @return array<string, mixed> */
    public function presentFromLoadedRelations(WalletAccount $account): array
    {
        return $this->format($account, $this->projection->snapshotFromLoadedRelations($account));
    }

    /**
     * @param array{total:string,blocked:string,available:string} $snapshot
     * @return array<string, mixed>
     */
    private function format(WalletAccount $account, array $snapshot): array
    {
        return [
            'reference' => (string) $account->code,
            'type' => $account->asset_type->value,
            'title' => (string) $account->title,
            'unit' => (string) $account->unit,
            'balance' => [
                'total' => (string) $snapshot['total'],
                'blocked' => (string) $snapshot['blocked'],
                'available' => (string) $snapshot['available'],
            ],
        ];
    }
}
