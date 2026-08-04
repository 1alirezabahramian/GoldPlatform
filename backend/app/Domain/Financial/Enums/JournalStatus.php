<?php

namespace App\Domain\Financial\Enums;

enum JournalStatus: string
{
    case DRAFT = 'draft';
    case POSTED = 'posted';
    case REVERSED = 'reversed';
}
