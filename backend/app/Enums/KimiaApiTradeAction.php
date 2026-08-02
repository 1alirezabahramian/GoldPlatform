<?php

namespace App\Enums;

enum KimiaApiTradeAction: int
{
    case BuyFromCustomer = 32;

    case SellToCustomer = 64;

    public static function fromTradeSide(KimiaTradeSide $side): self
    {
        return match ($side) {
            KimiaTradeSide::BuyFromCustomer => self::BuyFromCustomer,
            KimiaTradeSide::SellToCustomer => self::SellToCustomer,
        };
    }
}
