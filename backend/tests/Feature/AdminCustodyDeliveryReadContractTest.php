<?php

namespace Tests\Feature;

use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCustodyDeliveryReadContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_can_read_safe_custody_detail_without_metadata(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $customer = User::factory()->create();
        $custody = CustodyAsset::query()->create([
            'user_id' => $customer->id,
            'asset_type' => 'coin',
            'title' => 'Sample',
            'quantity' => '1.00000000',
            'status' => 'in_custody',
            'metadata' => ['secret' => 'hidden'],
        ]);

        $response = $this->getJson('/api/v1/admin/custodies/'.$custody->id)
            ->assertOk()
            ->assertJsonStructure(['data' => ['custody', 'timeline', 'delivery_requests'], 'meta']);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('metadata', $encoded);
        $this->assertStringNotContainsString('hidden', $encoded);
    }

    public function test_admin_can_read_delivery_detail_without_receiver_identity(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $customer = User::factory()->create();
        $custody = CustodyAsset::query()->create([
            'user_id' => $customer->id,
            'asset_type' => 'gold',
            'title' => 'Sample',
            'quantity' => '1.00000000',
            'status' => 'delivery_requested',
        ]);
        $delivery = DeliveryRequest::query()->create([
            'custody_asset_id' => $custody->id,
            'user_id' => $customer->id,
            'status' => 'requested',
            'receiver_name' => 'Private Name',
            'receiver_identifier' => 'PRIVATE-ID',
            'metadata' => ['secret' => 'hidden'],
        ]);

        $response = $this->getJson('/api/v1/admin/deliveries/'.$delivery->id)
            ->assertOk()
            ->assertJsonStructure(['data' => ['delivery', 'timeline', 'custody'], 'meta']);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('receiver_identifier', $encoded);
        $this->assertStringNotContainsString('PRIVATE-ID', $encoded);
        $this->assertStringNotContainsString('metadata', $encoded);
    }

    public function test_operator_cannot_access_admin_custody_list(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/custodies')->assertForbidden();
    }

    public function test_per_page_is_bounded(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/deliveries?per_page=51')->assertUnprocessable();
    }
}
