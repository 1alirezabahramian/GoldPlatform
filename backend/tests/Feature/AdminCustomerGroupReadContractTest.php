<?php

namespace Tests\Feature;

use App\Models\CustomerTradingPolicy;
use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCustomerGroupReadContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_can_read_groups_with_real_policies_without_metadata(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $group = UserGroup::query()->create([
            'title' => 'ویژه',
            'priority' => 1,
            'is_active' => true,
        ]);

        User::factory()->create(['group_id' => $group->id]);

        CustomerTradingPolicy::query()->create([
            'user_group_id' => $group->id,
            'requires_available_balance' => true,
            'allow_negative_balance' => false,
            'asset_lock_minutes' => 60,
            'max_gold_weight' => '50.00000000',
            'max_coin_quantity' => 10,
            'max_money_amount' => '1000000000.00',
            'credit_limit' => '0.00',
            'min_order_amount' => '100000.00',
            'max_order_amount' => '1000000000.00',
            'max_delivery_items' => 5,
            'is_active' => true,
            'metadata' => ['secret' => 'hidden'],
        ]);

        $response = $this->getJson('/api/v1/admin/customer-groups')
            ->assertOk()
            ->assertJsonPath('data.items.0.title', 'ویژه')
            ->assertJsonPath('data.items.0.users_count', 1)
            ->assertJsonPath('data.items.0.policies.0.asset_lock_minutes', 60)
            ->assertJsonMissingPath('data.items.0.policies.0.metadata');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('secret', $encoded);
    }

    public function test_operator_cannot_read_customer_groups(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/customer-groups')->assertForbidden();
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/customer-groups?status=unknown')->assertUnprocessable();
        $this->getJson('/api/v1/admin/customer-groups?per_page=100')->assertUnprocessable();
    }
}
