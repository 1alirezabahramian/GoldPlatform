<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $wallet = Wallet::create([
            'user_id' => $user->id,
        ]);

        $accounts = [
            ['code' => 'RIAL',      'title' => 'کیف پول ریالی'],
            ['code' => 'GOLD18',    'title' => 'طلای ۱۸ عیار'],
            ['code' => 'USD',       'title' => 'دلار آمریکا'],
            ['code' => 'EUR',       'title' => 'یورو'],
            ['code' => 'AED',       'title' => 'درهم امارات'],
            ['code' => 'TRY',       'title' => 'لیر ترکیه'],
            ['code' => 'BTC',       'title' => 'بیت کوین'],
            ['code' => 'BANK_COIN', 'title' => 'سکه بانکی'],
            ['code' => 'PAHLAVI',   'title' => 'سکه پهلوی'],
        ];

        foreach ($accounts as $account) {
            $wallet->accounts()->create($account);
        }
    }
}