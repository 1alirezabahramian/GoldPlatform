<?php

namespace Tests\Fixtures;

use App\Contracts\OutboxEventHandler;
use App\Models\OutboxMessage;

final class RecordingOutboxHandler implements OutboxEventHandler
{
    public static array $handled = [];

    public function handle(OutboxMessage $message): void
    {
        self::$handled[] = $message->uuid;
    }
}
