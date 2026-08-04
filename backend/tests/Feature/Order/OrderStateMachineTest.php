<?php

namespace Tests\Feature\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Order\OrderStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class OrderStateMachineTest extends TestCase
{
    use RefreshDatabase;

    private OrderStateMachine $machine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->machine = app(OrderStateMachine::class);
    }

    public function test_happy_path_reaches_completed_in_order(): void
    {
        $order = $this->order();

        $order = $this->machine->approve($order);
        $this->assertSame(OrderStatus::Approved, $order->status);
        $this->assertNotNull($order->approved_at);

        $order = $this->machine->startExecution($order);
        $this->assertSame(OrderStatus::Executing, $order->status);

        $order = $this->machine->startSettlement($order);
        $this->assertSame(OrderStatus::Settling, $order->status);

        $order = $this->machine->complete($order);
        $this->assertSame(OrderStatus::Completed, $order->status);
        $this->assertNotNull($order->completed_at);
        $this->assertSame(4, $order->state_version);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $this->expectException(LogicException::class);

        $this->machine->complete($this->order());
    }

    public function test_rejection_requires_reason_and_is_terminal(): void
    {
        $order = $this->machine->reject($this->order(), 'مدارک مشتری ناقص است.');

        $this->assertSame(OrderStatus::Rejected, $order->status);
        $this->assertSame('مدارک مشتری ناقص است.', $order->status_reason);
        $this->assertNotNull($order->rejected_at);

        $this->expectException(LogicException::class);
        $this->machine->approve($order);
    }

    public function test_failure_requires_reason(): void
    {
        $order = $this->machine->startExecution(
            $this->machine->approve($this->order())
        );

        $this->expectException(LogicException::class);
        $this->machine->fail($order, '');
    }

    public function test_order_cannot_expire_before_expires_at(): void
    {
        $order = $this->order(['expires_at' => now()->addMinute()]);

        $this->expectException(LogicException::class);
        $this->machine->expire($order);
    }

    public function test_expired_order_becomes_terminal(): void
    {
        $order = $this->order(['expires_at' => now()->subSecond()]);
        $order = $this->machine->expire($order);

        $this->assertSame(OrderStatus::Expired, $order->status);
        $this->assertNotNull($order->expired_at);

        $this->expectException(LogicException::class);
        $this->machine->approve($order);
    }

    public function test_repeating_same_transition_is_idempotent(): void
    {
        $order = $this->machine->approve($this->order());
        $version = $order->state_version;

        $same = $this->machine->approve($order);

        $this->assertSame($version, $same->state_version);
        $this->assertSame(OrderStatus::Approved, $same->status);
    }

    private function order(array $overrides = []): Order
    {
        return Order::query()->create(array_merge([
            'user_id' => 1,
            'type' => 'buy',
            'status' => OrderStatus::Pending,
            'gold_weight' => '1.000',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1000000',
        ], $overrides));
    }
}
