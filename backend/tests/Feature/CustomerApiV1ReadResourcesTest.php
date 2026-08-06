<?php

namespace Tests\Feature;

use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

final class CustomerApiV1ReadResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_read_lists_use_the_versioned_envelope_and_pagination_contract(): void
    {
        $customer = $this->customer();
        Sanctum::actingAs($customer);

        foreach (['orders', 'custodies', 'deliveries'] as $resource) {
            $response = $this->getJson("/api/v1/customer/{$resource}")
                ->assertOk()
                ->assertJsonPath('data.items', [])
                ->assertJsonPath('meta.api_version', 'v1')
                ->assertJsonStructure([
                    'data' => ['items'],
                    'meta' => [
                        'request_id',
                        'generated_at',
                        'api_version',
                        'pagination' => [
                            'current_page',
                            'per_page',
                            'total',
                            'last_page',
                            'has_more',
                        ],
                    ],
                    'message',
                ]);

            $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
            $this->assertStringNotContainsString('user_id', $encoded);
            $this->assertStringNotContainsString('external_asset_id', $encoded);
            $this->assertStringNotContainsString('external_product_id', $encoded);
            $this->assertStringNotContainsString('receiver_identifier', $encoded);
            $this->assertStringNotContainsString('metadata', $encoded);
        }
    }

    public function test_customer_order_list_excludes_another_customers_orders(): void
    {
        $customer = $this->customer();
        $otherCustomer = $this->customer();

        Order::query()->create([
            'user_id' => $otherCustomer->id,
            'type' => 'buy',
            'gold_weight' => '1.250',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1250000',
            'description' => 'Other customer order',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($customer);

        $this->getJson('/api/v1/customer/orders')
            ->assertOk()
            ->assertJsonPath('data.items', [])
            ->assertJsonPath('meta.pagination.total', 0);
    }

    public function test_customer_delivery_list_excludes_another_customers_deliveries(): void
    {
        $customer = $this->customer();
        $otherCustomer = $this->customer();
        $custody = CustodyAsset::query()->create([
            'user_id' => $otherCustomer->id,
            'asset_type' => 'gold',
            'title' => 'Other customer custody',
            'quantity' => '1.00000000',
            'weight' => '1.00000000',
            'fineness' => '750.0000',
            'branch_code' => 'TEST',
        ]);

        DeliveryRequest::query()->create([
            'custody_asset_id' => $custody->id,
            'user_id' => $otherCustomer->id,
            'branch_code' => 'TEST',
        ]);

        Sanctum::actingAs($customer);

        $this->getJson('/api/v1/customer/deliveries')
            ->assertOk()
            ->assertJsonPath('data.items', [])
            ->assertJsonPath('meta.pagination.total', 0);
    }

    private function customer(): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate('customer', 'web');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        return $customer;
    }
}
