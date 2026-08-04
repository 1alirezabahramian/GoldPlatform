<?php

namespace Tests\Feature;

use App\Models\OutboxMessage;
use App\Services\OutboxDispatcherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixtures\RecordingOutboxHandler;
use Tests\TestCase;

class OutboxDispatcherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RecordingOutboxHandler::$handled = [];
    }

    public function test_registered_event_is_processed_once(): void
    {
        config()->set('outbox.handlers.order.approved', RecordingOutboxHandler::class);

        $message = OutboxMessage::create([
            'event_type' => 'order.approved',
            'payload' => ['order_id' => 10],
        ]);

        $dispatcher = app(OutboxDispatcherService::class);
        $first = $dispatcher->dispatchPending();
        $second = $dispatcher->dispatchPending();

        $this->assertSame(1, $first['processed']);
        $this->assertSame(0, $second['processed']);
        $this->assertCount(1, RecordingOutboxHandler::$handled);
        $this->assertNotNull($message->fresh()->processed_at);
    }

    public function test_unknown_event_fails_closed_and_schedules_retry(): void
    {
        $message = OutboxMessage::create([
            'event_type' => 'unknown.event',
            'payload' => ['sensitive' => 'must-not-be-in-error'],
        ]);

        $result = app(OutboxDispatcherService::class)->dispatchPending();
        $message->refresh();

        $this->assertSame(1, $result['failed']);
        $this->assertSame(1, $message->attempts);
        $this->assertNotNull($message->available_at);
        $this->assertSame('RuntimeException', class_basename($message->last_error));
        $this->assertStringNotContainsString('must-not-be-in-error', (string) $message->last_error);
    }
}
