<?php

namespace Tests\Feature\Trading;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletAccount;
use App\Services\TradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TradeEngineCompletionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function approved_order_fails_closed_without_verified_kimia_result_and_creates_no_partial_records(): void
    {
        $user = User::query()->create([
            'mobile' => '09120000001',
            'name' => 'Trading Test User',
            'mobile_verified' => true,
            'is_active' => true,
        ]);

        $wallet = Wallet::query()->firstOrCreate(['user_id' => $user->id]);
        $from = $this->account($wallet, 'CUSTOMER-MONEY');
        $to = $this->account($wallet, 'PLATFORM-MONEY');

        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'approved',
            'gold_weight' => '1.250',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1250000',
        ]);

        try {
            app(TradeService::class)->execute($order, $from->id, $to->id, 'TOMAN');
            $this->fail('Expected trade execution to remain blocked until verified Kimia result evidence exists.');
        } catch (LogicException $exception) {
            $this->assertStringContainsString('verified Kimia', $exception->getMessage());
            $this->assertDatabaseCount('trades', 0);
            $this->assertDatabaseCount('financial_transactions', 0);
            $this->assertDatabaseCount('ledger_entries', 0);
            $this->assertDatabaseCount('settlements', 0);
            $this->assertSame('approved', $order->refresh()->status->value);
        }
    }

    #[Test]
    public function execution_rejects_non_approved_order_without_partial_records(): void
    {
        $user = User::query()->create([
            'mobile' => '09120000002',
            'mobile_verified' => true,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'pending',
            'gold_weight' => '1.000',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1000000',
        ]);

        try {
            app(TradeService::class)->execute($order, 1, 2, 'TOMAN');
            $this->fail('Expected execution to be rejected.');
        } catch (LogicException) {
            $this->assertDatabaseCount('trades', 0);
            $this->assertDatabaseCount('financial_transactions', 0);
            $this->assertDatabaseCount('ledger_entries', 0);
            $this->assertDatabaseCount('settlements', 0);
            $this->assertSame('pending', $order->refresh()->status->value);
        }
    }

    #[Test]
    public function execution_requires_explicit_distinct_accounts_and_asset_unit(): void
    {
        $user = User::query()->create([
            'mobile' => '09120000003',
            'mobile_verified' => true,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'sell',
            'status' => 'approved',
            'gold_weight' => '0.500',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '500000',
        ]);

        $this->expectException(InvalidArgumentException::class);
        app(TradeService::class)->execute($order, 7, 7, '');
    }

    private function account(Wallet $wallet, string $code): WalletAccount
    {
        return WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => $code,
            'title' => $code,
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);
    }
}
