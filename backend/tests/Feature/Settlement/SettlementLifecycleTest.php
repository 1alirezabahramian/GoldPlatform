<?php

namespace Tests\Feature\Settlement;

use App\Enums\SettlementStatus;
use App\Models\Order;
use App\Services\Settlement\SettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettlementLifecycleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_moves_a_settlement_from_pending_to_processing_to_completed(): void
    {
        $service = app(SettlementService::class);
        $order = $this->createOrder();

        $settlement = $service->createPending(
            order: $order,
            assetType: 'money',
            amount: '1250000.00000000',
            idempotencyKey: 'settlement:lifecycle:completed',
            metadata: ['source' => 'test'],
        );

        $processing = $service->startProcessing($settlement);

        $this->assertSame(SettlementStatus::Processing, $processing->status);
        $this->assertNotNull($processing->processing_started_at);

        $completed = $service->complete(
            settlement: $processing,
            kimiaReference: 'kimia-record-1001',
            metadata: ['result' => ['record_id' => 1001]],
        );

        $this->assertSame(SettlementStatus::Completed, $completed->status);
        $this->assertSame('kimia-record-1001', $completed->kimia_reference);
        $this->assertNotNull($completed->completed_at);
        $this->assertNull($completed->failure_reason);
        $this->assertSame('test', $completed->metadata['source']);
        $this->assertSame(1001, $completed->metadata['result']['record_id']);
    }

    #[Test]
    public function it_marks_a_pending_settlement_as_failed_without_changing_its_amount(): void
    {
        $service = app(SettlementService::class);
        $settlement = $service->createPending(
            order: $this->createOrder(),
            assetType: 'gold',
            amount: '1.25000000',
            idempotencyKey: 'settlement:lifecycle:failed',
        );

        $failed = $service->fail($settlement, 'Kimia connection failed', [
            'retryable' => true,
        ]);

        $this->assertSame(SettlementStatus::Failed, $failed->status);
        $this->assertSame('1.25000000', $failed->amount);
        $this->assertSame('Kimia connection failed', $failed->failure_reason);
        $this->assertTrue($failed->metadata['retryable']);
        $this->assertNotNull($failed->failed_at);
    }

    #[Test]
    public function it_rejects_completion_before_processing(): void
    {
        $service = app(SettlementService::class);
        $settlement = $service->createPending(
            order: $this->createOrder(),
            assetType: 'money',
            amount: '1000.00000000',
            idempotencyKey: 'settlement:lifecycle:invalid',
        );

        $this->expectException(LogicException::class);

        $service->complete($settlement);
    }

    private function createOrder(): Order
    {
        return Order::query()->create([
            'type' => 'buy',
            'status' => 'approved',
            'gold_weight' => '1.250',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1250000',
        ]);
    }
}
