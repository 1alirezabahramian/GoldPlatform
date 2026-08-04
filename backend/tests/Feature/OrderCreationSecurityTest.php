<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderCreationSecurityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_users_cannot_create_orders(): void
    {
        $this->postJson('/api/orders', $this->validPayload(), $this->headers('unauthenticated'))
            ->assertUnauthorized();
    }

    #[Test]
    public function authenticated_order_uses_the_authenticated_user_id(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/orders', $this->validPayload(), $this->headers('create-order'))
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function clients_cannot_override_order_owner_or_server_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/orders', array_merge($this->validPayload(), [
            'user_id' => $user->id + 1000,
            'status' => 'completed',
            'total_price' => 1,
        ]), $this->headers('override-attempt'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id', 'status', 'total_price']);
    }

    private function validPayload(): array
    {
        return [
            'type' => 'buy',
            'gold_weight' => '1.250',
            'gold_price' => 1000000,
            'commission' => 0,
            'description' => 'Test order',
        ];
    }

    private function headers(string $key): array
    {
        return ['Idempotency-Key' => $key];
    }
}
