<?php

namespace App\Services;

use App\Models\WalletAccount;
use LogicException;

/**
 * @deprecated Financial balances for Money, Gold, Coin and Currency are owned by Kimia.
 *
 * This duplicate legacy service is intentionally fail-closed and remains only until
 * all historical dependencies have been identified and migrated.
 */
class WalletService
{
    public function deposit(int $walletAccountId, string $amount): WalletAccount
    {
        throw new LogicException($this->disabledMessage());
    }

    public function withdraw(int $walletAccountId, string $amount): WalletAccount
    {
        throw new LogicException($this->disabledMessage());
    }

    private function disabledMessage(): string
    {
        return 'Local customer financial balance mutation is disabled; Kimia is the source of truth.';
    }
}
