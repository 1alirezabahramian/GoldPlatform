<?php

namespace App\Domain\Trading\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case SETTLED = 'settled';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}
