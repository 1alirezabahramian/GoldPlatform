<?php

namespace App\Observers;

use App\Exceptions\BusinessException;
use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    /**
     * A Kimia account may be linked once, but an established binding is immutable.
     *
     * @throws BusinessException
     */
    public function updating(User $user): void
    {
        if (
            $user->isDirty('account_id')
            && $user->getOriginal('account_id') !== null
        ) {
            throw new BusinessException(
                'A linked Kimia account cannot be changed or removed.'
            );
        }
    }

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
