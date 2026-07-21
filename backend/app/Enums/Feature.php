<?php

namespace App\Enums;

enum Feature: string
{
    case Dashboard   = 'dashboard';

    case Wallet      = 'wallet';

    case Buy         = 'buy';

    case Sell        = 'sell';

    case Orders      = 'orders';

    case Reports     = 'reports';

    case Api         = 'api';

    case Kimia       = 'kimia';

    case Sms         = 'sms';

    case Jibit       = 'jibit';

    case MultiBranch = 'multi_branch';
}