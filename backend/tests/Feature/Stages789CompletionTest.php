<?php

namespace Tests\Feature;

use App\Enums\CustodyStatus;
use App\Enums\DeliveryStatus;
use App\Models\CustodyAsset;
use App\Models\CustomerTradingPolicy;
use App\Models\User;
use App\Services\CustodyService;
use App\Services\DeliveryService;
use App\Services\TradingPolicyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use LogicException;
use Tests\TestCase;

class Stages789CompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_custody_is_independent_idempotent_and_terminal_safe(): void
    {
        $user = User::factory()->create();
        $service = app(CustodyService::class);
        $uuid = fake()->uuid();

        $asset = $service->receive([
            'uuid' => $uuid,
            'user_id' => $user->id,
            'asset_type' => 'parsian',
            'external_product_id' => 500,
            'title' => 'پارسیان ۵۰۰ سوت',
            'quantity' => '1',
            'weight' => '0.500',
            'fineness' => '750',
        ]);
        $same = $service->receive(['uuid' => $uuid, 'user_id' => $user->id, 'asset_type' => 'parsian', 'title' => 'duplicate']);

        $this->assertSame($asset->id, $same->id);
        $closed = $service->closeAs($asset, CustodyStatus::ConvertedToGold, 'ledger:123');
        $this->assertSame(CustodyStatus::ConvertedToGold, $closed->status);

        $this->expectException(LogicException::class);
        $service->reserve($closed);
    }

    public function test_delivery_flow_prevents_double_delivery_and_records_receiver(): void
    {
        $owner = User::factory()->create();
        $operator = User::factory()->create();
        $asset = app(CustodyService::class)->receive([
            'user_id' => $owner->id,
            'asset_type' => 'bullion',
            'title' => 'شمش ۲۴ عیار',
            'quantity' => '1',
            'weight' => '1.000',
            'branch_code' => 'SHZ-01',
        ]);

        $delivery = app(DeliveryService::class);
        $request = $delivery->request($asset, $owner, ['branch_code' => 'SHZ-01']);
        $request = $delivery->approve($request, $operator);
        $request = $delivery->markReady($request);
        $request = $delivery->deliver($request, $operator, 'علیرضا بهرامیان', 'verified-id');

        $this->assertSame(DeliveryStatus::Delivered, $request->status);
        $this->assertSame('verified-id', $request->receiver_identifier);
        $this->assertSame(CustodyStatus::Delivered, $request->custodyAsset->fresh()->status);

        $this->expectException(LogicException::class);
        $delivery->deliver($request, $operator, 'علیرضا بهرامیان', 'verified-id');
    }

    public function test_group_policy_enforces_confirmed_limits_without_hardcoding_ids(): void
    {
        $groupId = DB::table('user_groups')->insertGetId([
            'title' => 'ویژه',
            'buy_commission' => 0,
            'sell_commission' => 1,
            'discount_percent' => 0,
            'priority' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::factory()->create(['group_id' => $groupId]);
        CustomerTradingPolicy::query()->create([
            'user_group_id' => $groupId,
            'requires_available_balance' => false,
            'allow_negative_balance' => true,
            'asset_lock_minutes' => 60,
            'max_gold_weight' => '50',
            'max_coin_quantity' => 10,
            'max_money_amount' => '1000000000',
            'is_active' => true,
        ]);

        $policy = app(TradingPolicyService::class)->assertOrderAllowed($user, [
            'asset_type' => 'gold',
            'quantity' => '49.999',
            'total_amount' => '999999999',
        ]);
        $this->assertSame(60, $policy->asset_lock_minutes);
        $this->assertTrue($policy->allow_negative_balance);

        $this->expectException(LogicException::class);
        app(TradingPolicyService::class)->assertOrderAllowed($user, [
            'asset_type' => 'coin',
            'quantity' => 11,
            'total_amount' => '1000',
        ]);
    }
}
