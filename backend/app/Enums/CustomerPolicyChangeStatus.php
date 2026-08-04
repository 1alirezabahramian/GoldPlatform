<?php

namespace App\Enums;

enum CustomerPolicyChangeStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Applied = 'applied';

    public function isTerminalForReview(): bool
    {
        return in_array($this, [self::Approved, self::Rejected, self::Applied], true);
    }
}
