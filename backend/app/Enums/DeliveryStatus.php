<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Ready = 'ready';
    case Delivered = 'delivered';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function isTerminal(): bool
    {
        return in_array($this, [self::Delivered, self::Rejected, self::Cancelled], true);
    }
}
