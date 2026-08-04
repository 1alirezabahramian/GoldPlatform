<?php

namespace App\Enums;

enum CustodyStatus: string
{
    case InCustody = 'in_custody';
    case Reserved = 'reserved';
    case DeliveryRequested = 'delivery_requested';
    case ReadyForPickup = 'ready_for_pickup';
    case Delivered = 'delivered';
    case Resold = 'resold';
    case ConvertedToGold = 'converted_to_gold';
    case ConvertedToMoney = 'converted_to_money';
    case Cancelled = 'cancelled';

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Delivered,
            self::Resold,
            self::ConvertedToGold,
            self::ConvertedToMoney,
            self::Cancelled,
        ], true);
    }
}
