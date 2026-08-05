<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\WalletTransaction;
use LogicException;

/**
 * Historical compatibility shell.
 *
 * Money, Gold, Coin and Currency balances are authoritative in Kimia.
 * GoldPlatform must not mutate a competing customer financial balance.
 */
class WalletService
{
    public const CUSTOMER_FINANCIAL_MUTATIONS_ENABLED = false;

    /**
     * @deprecated Internal customer financial deposits are disabled.
     */
    public function deposit(
        User $user,
        string $code,
        string $amount,
        ?string $reference = null,
        ?string $description = null
    ): WalletTransaction {
        throw new LogicException(
            'Internal wallet deposits are disabled. Customer financial balances are sourced from Kimia.'
        );
    }

    /**
     * @deprecated Internal customer financial withdrawals are disabled.
     */
    public function withdraw(
        User $user,
        string $code,
        string $amount,
        ?string $reference = null,
        ?string $description = null
    ): WalletTransaction {
        throw new LogicException(
            'Internal wallet withdrawals are disabled. Customer financial balances are sourced from Kimia.'
        );
    }
}
