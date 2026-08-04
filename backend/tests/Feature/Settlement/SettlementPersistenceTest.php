<?php

namespace Tests\Feature\Settlement;

use App\Enums\SettlementStatus;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettlementPersistenceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_persists_a_pending_settlement_with_precise_amount_and_idempotency_key(): void
    {
        $user = User::factory()->create();

        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'approved',
            'gold_weight' => '1.250',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1250000',
        ]);

        $settlement = Settlement::query()->create([
            'order_id' => $order->id,
            'status' => SettlementStatus::Pending,
            'asset_type' => 'gold',
            'amount' => '1.25000000',
            'idempotency_key' => 'settlement:test:1',
        ]);

        $this->assertNotNull($settlement->uuid);
        $this->assertSame(SettlementStatus::Pending, $settlement->status);
        $this->assertSame('1.25000000', $settlement->amount);
        $this->assertSame($order->id, $settlement->order_id);
    }
}
