<?php

namespace App\Services;

use App\Models\OutboxMessage;
use Illuminate\Database\Eloquent\Model;

class OutboxService
{
    public function enqueue(string $eventType, array $payload, ?Model $aggregate = null): OutboxMessage
    {
        return OutboxMessage::query()->create([
            'event_type' => $eventType,
            'aggregate_type' => $aggregate?->getMorphClass(),
            'aggregate_id' => $aggregate ? (string) $aggregate->getKey() : null,
            'payload' => $payload,
            'available_at' => now(),
        ]);
    }
}
