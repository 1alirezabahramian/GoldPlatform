<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    /**
     * Create wallet and default accounts after user creation.
     */
    public function created(User $user): void
    {
        $wallet = Wallet::create([
            'user_id' => $user->id,
        ]);

        $accounts = [
            ['code' => 'RIAL', 'title' => 'کیف پول ریالی'],
            ['code' => 'GOLD18', 'title' => 'طلای ۱۸ عیار'],
        ];

        foreach ($accounts as $account) {
            $wallet->accounts()->create($account);
        }
    }
}
