<?php

namespace App\Services\Wallet;

use App\Models\WalletAccount;
use App\Support\Decimal;

class BalanceProjectionService
{
    /** @return array{total:string,blocked:string,available:string} */
    public function snapshot(WalletAccount $account): array
    {
        $total = '0.00000000';

        $account->ledgerEntries()
            ->get(['entry_type', 'amount'])
            ->each(function ($entry) use (&$total): void {
                $total = $entry->entry_type === 'credit'
                    ? Decimal::add($total, (string) $entry->amount)
                    : Decimal::subtract($total, (string) $entry->amount);
            });

        $blocked = '0.00000000';
        $account->balanceReservations()
            ->where('status', 'active')
            ->get(['amount'])
            ->each(function ($reservation) use (&$blocked): void {
                $blocked = Decimal::add($blocked, (string) $reservation->amount);
            });

        return [
            'total' => Decimal::normalize($total),
            'blocked' => Decimal::normalize($blocked),
            'available' => Decimal::subtract($total, $blocked),
        ];
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
}
