<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminOrderReadContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_can_read_paginated_orders_without_sensitive_fields(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        Order::query()->create([
            'user_id' => $admin->id,
            'type' => 'buy',
            'asset_type' => 'gold',
            'asset_quantity' => '1.00000000',
            'asset_unit' => 'gram',
            'status' => 'pending',
            'state_version' => 1,
        ]);

        $response = $this->getJson('/api/v1/admin/orders')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['items', 'pagination'],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ]);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('metadata', $encoded);
        $this->assertStringNotContainsString('kimia_reference', $encoded);
        $this->assertStringNotContainsString('idempotency_key', $encoded);
    }

    public function test_admin_can_read_order_detail_with_timeline_and_summaries(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $order = Order::query()->create([
            'user_id' => $admin->id,
            'type' => 'sell',
            'asset_type' => 'gold',
            'asset_quantity' => '2.00000000',
            'asset_unit' => 'gram',
            'status' => 'pending',
            'state_version' => 1,
        ]);

        $this->getJson('/api/v1/admin/orders/'.$order->id)
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['order', 'timeline', 'trades', 'settlements'],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ]);
    }

    public function test_operator_cannot_access_admin_orders(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/orders')->assertForbidden();
    }

    public function test_per_page_above_limit_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/orders?per_page=51')->assertUnprocessable();
    }
}
