<?php

namespace App\Domain\Trading\Enums;

enum QuoteStatus: string
{
    case REQUESTED = 'requested';
    case FROZEN = 'frozen';
    case USED = 'used';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}
