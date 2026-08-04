<?php

namespace App\Domain\Financial\Enums;

enum JournalSide: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public function opposite(): self
    {
        return $this === self::DEBIT
            ? self::CREDIT
            : self::DEBIT;
    }
}
