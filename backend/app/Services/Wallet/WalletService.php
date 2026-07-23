<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    /**
     * Deposit amount into a wallet account.
     */
    public function deposit(
        User $user,
        string $code,
        string $amount,
        ?string $reference = null,
        ?string $description = null
    ): WalletTransaction {

        return DB::transaction(function () use (
            $user,
            $code,
            $amount,
            $reference,
            $description
        ) {

            $wallet = $user->wallet;

            if (! $wallet) {
                throw new RuntimeException('Wallet not found.');
            }

            $account = $wallet
                ->accounts()
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            if (! $account) {
                throw new RuntimeException('Wallet account not found.');
            }

            $balance = bcadd(
                $account->balance,
                $amount,
                6
            );

            $account->update([
                'balance' => $balance,
            ]);

            return WalletTransaction::create([
                'wallet_account_id' => $account->id,
                'wallet_type'       => $code,
                'type'              => 'deposit',
                'amount'            => $amount,
                'balance_after'     => $balance,
                'reference'         => $reference,
                'description'       => $description,
            ]);
        });
    }

    /**
     * Withdraw amount from a wallet account.
     */
    public function withdraw(
        User $user,
        string $code,
        string $amount,
        ?string $reference = null,
        ?string $description = null
    ): WalletTransaction {

        return DB::transaction(function () use (
            $user,
            $code,
            $amount,
            $reference,
            $description
        ) {

            $wallet = $user->wallet;

            if (! $wallet) {
                throw new RuntimeException('Wallet not found.');
            }

            $account = $wallet
                ->accounts()
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            if (! $account) {
                throw new RuntimeException('Wallet account not found.');
            }

            if (bccomp($account->balance, $amount, 6) < 0) {
                throw new RuntimeException('Insufficient balance.');
            }

            $balance = bcsub(
                $account->balance,
                $amount,
                6
            );

            $account->update([
                'balance' => $balance,
            ]);

            return WalletTransaction::create([
                'wallet_account_id' => $account->id,
                'wallet_type'       => $code,
                'type'              => 'withdraw',
                'amount'            => $amount,
                'balance_after'     => $balance,
                'reference'         => $reference,
                'description'       => $description,
            ]);
        });
    }
}