<?php

namespace Tests\Feature;

use App\Models\Settlement;
use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminSettlementReadContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_can_read_safe_settlement_detail_without_sensitive_fields(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $settlement = Settlement::query()->create([
            'status' => 'failed',
            'asset_type' => 'money',
            'amount' => '125000.00',
            'failure_reason' => 'Connection timeout while calling provider',
            'kimia_reference' => 'secret-kimia-reference',
            'idempotency_key' => 'secret-idempotency-key',
            'metadata' => ['secret' => 'hidden'],
            'failed_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/admin/settlements/'.$settlement->id)
            ->assertOk()
            ->assertJsonPath('data.settlement.reference', $settlement->uuid)
            ->assertJsonPath('data.settlement.has_failure', true)
            ->assertJsonPath('data.settlement.failure_category', 'timeout')
            ->assertJsonStructure([
                'data' => ['settlement', 'timeline', 'order', 'trade'],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ]);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('secret-kimia-reference', $encoded);
        $this->assertStringNotContainsString('secret-idempotency-key', $encoded);
        $this->assertStringNotContainsString('Connection timeout while calling provider', $encoded);
        $this->assertStringNotContainsString('metadata', $encoded);
    }

    public function test_operator_cannot_read_admin_settlements(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/settlements')->assertForbidden();
    }

    public function test_settlement_list_rejects_invalid_status_and_large_page_size(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/settlements?status=retrying')->assertUnprocessable();
        $this->getJson('/api/v1/admin/settlements?per_page=51')->assertUnprocessable();
    }
}
