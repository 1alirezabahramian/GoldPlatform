<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminKimiaReadContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_can_view_safe_kimia_configuration_state_without_secrets(): void
    {
        config()->set('services.kimia.base_url', 'https://kimia.example.test');
        config()->set('services.kimia.username', 'secret-user');
        config()->set('services.kimia.password', 'secret-pass');
        config()->set('services.kimia.read_only', true);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/kimia/overview')
            ->assertOk()
            ->assertJsonPath('data.configuration.configured', true)
            ->assertJsonPath('data.mode.read_only', true)
            ->assertJsonPath('data.mode.write_enabled', false)
            ->assertJsonPath('data.observability.connection_status', 'not_probed')
            ->assertJsonPath('meta.api_version', 'v1');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('secret-user', $encoded);
        $this->assertStringNotContainsString('secret-pass', $encoded);
        $this->assertStringNotContainsString('https://kimia.example.test', $encoded);
    }

    public function test_operator_cannot_view_kimia_overview(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/kimia/overview')->assertForbidden();
    }
}
