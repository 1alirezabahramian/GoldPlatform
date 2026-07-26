<?php

namespace App\Services;

use App\Models\WalletAccount;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function deposit(int $walletAccountId, string $amount): WalletAccount
    {
        return DB::transaction(function () use ($walletAccountId, $amount) {

            $account = WalletAccount::lockForUpdate()->findOrFail($walletAccountId);

            $account->balance = bcadd($account->balance, $amount, 6);

            $account->save();

            return $account;
        });
    }

    public function withdraw(int $walletAccountId, string $amount): WalletAccount
    {
        return DB::transaction(function () use ($walletAccountId, $amount) {

            $account = WalletAccount::lockForUpdate()->findOrFail($walletAccountId);

            $account->balance = bcsub($account->balance, $amount, 6);

            $account->save();

            return $account;
        });
    }
}