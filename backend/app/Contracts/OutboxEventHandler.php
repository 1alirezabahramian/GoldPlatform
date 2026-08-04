<?php

namespace App\Contracts;

use App\Models\OutboxMessage;

interface OutboxEventHandler
{
    public function handle(OutboxMessage $message): void;
}
