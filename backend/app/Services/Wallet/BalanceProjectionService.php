<?php

namespace App\Services\Wallet;

use App\Models\WalletAccount;
use App\Support\Decimal;
use LogicException;

/**
 * Internal ledger-derived projection used only for audit, traceability,
 * reservations, workflow support and reconciliation.
 *
 * This service is NOT a source of truth for customer Money, Gold, Coin or
 * Currency balances. Customer-facing balances must come from Kimia reads.
 */
class BalanceProjectionService
{
    public const PURPOSE = 'audit_reconciliation_only';

    public const CUSTOMER_BALANCE_SOURCE = false;

    /** @return array{total:string,blocked:string,available:string} */
    public function snapshot(WalletAccount $account): array
    {
        return $this->calculate(
            $account->ledgerEntries()->get(['entry_type', 'amount']),
            $account->balanceReservations()->where('status', 'active')->get(['amount']),
        );
    }

    /**
     * Uses explicitly eager-loaded relations for internal read models without
     * changing the financial calculation. Both relations must be complete for
     * the intended audit/reconciliation snapshot; callers own that loading
     * contract.
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

    /**
     * Rebuilds the internal projection cache only.
     *
     * The persisted values must never be presented as authoritative customer
     * balances and remain rebuildable from internal ledger evidence.
     */
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
