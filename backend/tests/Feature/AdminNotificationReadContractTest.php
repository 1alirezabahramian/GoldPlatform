<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminNotificationReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_read_safe_notification_overview(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/notifications/overview');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonPath('data.channels.telegram.supported', false)
            ->assertJsonPath('data.channels.push.supported', false)
            ->assertJsonStructure([
                'data' => [
                    'channels' => ['in_app', 'sms', 'email', 'telegram', 'push'],
                    'outbox',
                    'capabilities',
                ],
                'meta',
                'message',
            ]);

        $payload = strtolower(json_encode($response->json(), JSON_THROW_ON_ERROR));
        $this->assertStringNotContainsString('api_key', $payload);
        $this->assertStringNotContainsString('password', $payload);
        $this->assertStringNotContainsString('token', $payload);
    }

    public function test_operator_cannot_read_notification_overview(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->getJson('/api/v1/admin/notifications/overview')
            ->assertForbidden();
    }
}
