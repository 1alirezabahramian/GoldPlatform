<?php

namespace App\Enums;

enum WalletAccountType: string
{
    case CASH = 'cash';

    case GOLD = 'gold';

    case COIN = 'coin';

    case CURRENCY = 'currency';
}