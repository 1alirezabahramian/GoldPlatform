<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminOperatorPermissionCatalog;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OperatorSafeDeliveryActionsContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_actor_cannot_use_delivery_actions(): void
    {
        $this->postJson('/api/v1/operator/deliveries/1/approve')->assertUnauthorized();
    }

    public function test_operator_without_specific_permission_is_forbidden(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        $operator->roles->first()->revokePermissionTo(AdminOperatorPermissionCatalog::DELIVERIES_APPROVE);

        $this->actingAs($operator)
            ->postJson('/api/v1/operator/deliveries/1/approve', [], ['Idempotency-Key' => 'test-approve-1'])
            ->assertForbidden();
    }

    public function test_order_approval_is_not_exposed_in_operator_v1(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->postJson('/api/v1/operator/orders/1/approve', [], ['Idempotency-Key' => 'test-order-1'])
            ->assertNotFound();
    }

    public function test_complete_requires_idempotency_key_before_execution(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->postJson('/api/v1/operator/deliveries/1/complete', [
                'receiver_name' => 'Receiver',
                'receiver_identifier' => 'ID-1',
            ])
            ->assertStatus(422);
    }
}
