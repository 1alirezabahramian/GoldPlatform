<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\WalletTransaction;
use LogicException;

/**
 * @deprecated Financial balances for Money, Gold, Coin and Currency are owned by Kimia.
 *
 * This legacy service is intentionally fail-closed. It is preserved temporarily for
 * dependency discovery and historical compatibility, but it must not mutate a local
 * customer financial balance.
 */
class WalletService
{
    public function deposit(
        User $user,
        string $code,
        string $amount,
        ?string $reference = null,
        ?string $description = null
    ): WalletTransaction {
        throw new LogicException($this->disabledMessage());
    }

    public function withdraw(
        User $user,
        string $code,
        string $amount,
        ?string $reference = null,
        ?string $description = null
    ): WalletTransaction {
        throw new LogicException($this->disabledMessage());
    }

    private function disabledMessage(): string
    {
        return 'Local customer financial balance mutation is disabled; Kimia is the source of truth.';
    }
}
