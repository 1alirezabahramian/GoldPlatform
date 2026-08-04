<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class OperatorQueueWorkspaceContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_read_versioned_queues_without_sensitive_fields(): void
    {
        $role = Role::findOrCreate('operator');
        $user = User::factory()->create();
        $user->assignRole($role);

        foreach (['/api/v1/operator/orders/queue', '/api/v1/operator/deliveries/queue'] as $uri) {
            $response = $this->actingAs($user)->getJson($uri);
            $response->assertOk()->assertJsonStructure(['data', 'meta', 'message']);
            $content = $response->getContent();
            $this->assertStringNotContainsString('receiver_identifier', $content);
            $this->assertStringNotContainsString('metadata', $content);
            $this->assertStringNotContainsString('external_asset_id', $content);
        }
    }

    public function test_invalid_queue_status_is_rejected(): void
    {
        $role = Role::findOrCreate('operator');
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->getJson('/api/v1/operator/orders/queue?status=unknown')->assertStatus(422);
    }
}
