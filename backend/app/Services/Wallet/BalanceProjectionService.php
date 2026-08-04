<?php

namespace App\Services\Wallet;

use App\Models\WalletAccount;
use App\Support\Decimal;
use LogicException;

class BalanceProjectionService
{
    /** @return array{total:string,blocked:string,available:string} */
    public function snapshot(WalletAccount $account): array
    {
        return $this->calculate(
            $account->ledgerEntries()->get(['entry_type', 'amount']),
            $account->balanceReservations()->where('status', 'active')->get(['amount']),
        );
    }

    /**
     * Uses explicitly eager-loaded relations for read models without changing
     * the financial calculation. Both relations must be complete for the
     * intended snapshot; callers own that loading contract.
     *
     * @return array{total:string,blocked:string,available:string}
     */
    public function snapshotFromLoadedRelations(WalletAccount $account): array
    {
        if (! $account->relationLoaded('ledgerEntries') || ! $account->relationLoaded('balanceReservations')) {
            throw new LogicException('Balance snapshot relations were not preloaded.');
        }

        return $this->calculate(
            $account->getRelation('ledgerEntries'),
            $account->getRelation('balanceReservations'),
        );
    }

    public function rebuild(WalletAccount $account): WalletAccount
    {
        $snapshot = $this->snapshot($account);

        $account->forceFill([
            'balance' => $snapshot['total'],
            'blocked_balance' => $snapshot['blocked'],
        ])->save();

        return $account->refresh();
    }

    /** @return array{total:string,blocked:string,available:string} */
    private function calculate(iterable $entries, iterable $reservations): array
    {
        $total = '0.00000000';

        foreach ($entries as $entry) {
            $total = $entry->entry_type === 'credit'
                ? Decimal::add($total, (string) $entry->amount)
                : Decimal::subtract($total, (string) $entry->amount);
        }

        $blocked = '0.00000000';

        foreach ($reservations as $reservation) {
            $blocked = Decimal::add($blocked, (string) $reservation->amount);
        }

        return [
            'total' => Decimal::normalize($total),
            'blocked' => Decimal::normalize($blocked),
            'available' => Decimal::subtract($total, $blocked),
        ];
    }
}
