<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerOrderStatusContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_catalog_requires_authentication(): void
    {
        $this->getJson('/api/v1/customer/order-statuses')->assertUnauthorized();
    }

    public function test_order_status_catalog_matches_backend_enum_exactly(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $response = $this->getJson('/api/v1/customer/order-statuses')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => ['code', 'is_terminal'],
                    ],
                ],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ]);

        $expected = collect(OrderStatus::cases())
            ->map(fn (OrderStatus $status) => [
                'code' => $status->value,
                'is_terminal' => $status->isTerminal(),
            ])
            ->values()
            ->all();

        $this->assertSame($expected, $response->json('data.items'));
    }

    public function test_terminal_flags_follow_existing_state_machine(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $items = collect($this->getJson('/api/v1/customer/order-statuses')
            ->assertOk()
            ->json('data.items'))
            ->keyBy('code');

        foreach (['completed', 'rejected', 'expired', 'cancelled', 'failed'] as $terminal) {
            $this->assertTrue($items[$terminal]['is_terminal']);
        }

        foreach (['pending', 'approved', 'executing', 'settling'] as $active) {
            $this->assertFalse($items[$active]['is_terminal']);
        }
    }
}
