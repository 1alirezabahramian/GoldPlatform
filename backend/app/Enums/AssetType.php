<?php

namespace App\Enums;

enum AssetType: string
{
    case Money = 'money';
    case Gold = 'gold';
    case Coin = 'coin';
    case Currency = 'currency';

    public function requiresExternalAssetId(): bool
    {
        return in_array($this, [self::Coin, self::Currency], true);
    }
}
