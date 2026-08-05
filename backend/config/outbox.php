<?php

return [
    'dispatch_enabled' => (bool) env('OUTBOX_DISPATCH_ENABLED', false),
    'batch_size' => (int) env('OUTBOX_BATCH_SIZE', 50),
    'max_attempts' => (int) env('OUTBOX_MAX_ATTEMPTS', 5),
    'retry_delay_seconds' => (int) env('OUTBOX_RETRY_DELAY_SECONDS', 60),

    /*
    | Event type => handler class implementing OutboxEventHandler.
    | Intentionally empty until a concrete delivery destination is approved.
    */
    'handlers' => [],
];
