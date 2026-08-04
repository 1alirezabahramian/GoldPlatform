<?php

namespace App\Domain\Financial\Enums;

enum FinancialAssetType: string
{
    case MONEY = 'money';
    case GOLD = 'gold';
    case COIN = 'coin';
    case CURRENCY = 'currency';
}
