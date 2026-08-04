<?php

namespace App\Domain\Trading\Order;

enum OrderTerminalAction: string
{
    case EXPIRE = 'expire';
    case CANCEL = 'cancel';
}
