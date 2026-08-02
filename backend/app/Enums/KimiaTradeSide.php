<?php

namespace App\Enums;

use InvalidArgumentException;

enum KimiaTradeSide: string
{
    case BuyFromCustomer = 'buy_from_customer';

    case SellToCustomer = 'sell_to_customer';

    public static function fromCustomerOrderType(string $orderType): self
    {
        return match ($orderType) {
            'buy' => self::SellToCustomer,
            'sell' => self::BuyFromCustomer,
            default => throw new InvalidArgumentException(
                "Unsupported customer order type: {$orderType}"
            ),
        };
    }
}
