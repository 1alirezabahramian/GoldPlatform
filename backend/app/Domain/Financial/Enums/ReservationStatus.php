<?php

namespace App\Domain\Financial\Enums;

enum ReservationStatus: string
{
    case ACTIVE = 'active';
    case RELEASED = 'released';
    case CAPTURED = 'captured';
    case EXPIRED = 'expired';
}
