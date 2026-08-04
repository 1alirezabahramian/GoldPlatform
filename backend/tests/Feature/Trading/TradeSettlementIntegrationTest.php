<?php

namespace Tests\Feature\Trading;

use App\Models\Order;
use App\Models\Settlement;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletAccount;
use App\Services\TradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TradeSettlementIntegrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_executes_an_approved_order_through_trade_ledger_and_settlement_once(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::query()->firstOrCreate(['user_id' => $user->id]);

        $from = WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => 'IRR-CUSTOMER',
            'title' => 'Customer money',
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);

        $to = WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => 'IRR-PLATFORM',
            'title' => 'Platform money',
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'approved',
            'gold_weight' => '1.250',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1250000',
        ]);

        $service = app(TradeService::class);

        $first = $service->execute(
            order: $order,
            fromAccountId: $from->id,
            toAccountId: $to->id,
            idempotencyKey: 'trade:settlement:test:1',
        );

        $second = $service->execute(
            order: $order->refresh(),
            fromAccountId: $from->id,
            toAccountId: $to->id,
            idempotencyKey: 'trade:settlement:test:1',
        );

        $this->assertSame($first->id, $second->id);
        $this->assertSame('completed', $order->refresh()->status);
        $this->assertSame(1, $order->trades()->count());
        $this->assertSame(2, $first->financialTransaction->ledgerEntries()->count());

        $settlement = Settlement::query()->sole();
        $this->assertSame('completed', $settlement->status->value);
        $this->assertSame($first->id, $settlement->trade_id);
        $this->assertSame($first->financial_transaction_id, $settlement->financial_transaction_id);
    }

    #[Test]
    public function it_rejects_execution_when_order_is_not_approved(): void
    {
        $user = User::factory()->create();
        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'pending',
            'gold_weight' => '1.000',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1000000',
        ]);

        $this->expectException(\LogicException::class);

        app(TradeService::class)->execute(
            order: $order,
            fromAccountId: 1,
            toAccountId: 2,
            idempotencyKey: 'trade:settlement:test:invalid',
        );
    }
}
