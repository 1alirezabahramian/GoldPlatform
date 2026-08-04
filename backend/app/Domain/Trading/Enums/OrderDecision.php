<?php

namespace App\Domain\Trading\Enums;

enum OrderDecision: string
{
    case APPROVE = 'approve';
    case REJECT = 'reject';
}
