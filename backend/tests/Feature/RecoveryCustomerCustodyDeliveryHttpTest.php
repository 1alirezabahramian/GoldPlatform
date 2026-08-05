<?php

namespace Tests\Feature;

use App\Models\CustodyAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

final class RecoveryCustomerCustodyDeliveryHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_read_own_custody_by_public_uuid(): void
    {
        $customer = $this->customer();
        $asset = $this->custodyFor($customer);

        $this->actingAs($customer, 'sanctum')
            ->getJson("/api/v1/customer/custodies/{$asset->uuid}")
            ->assertOk()
            ->assertJsonPath('data.custody.reference', $asset->uuid)
            ->assertJsonMissingPath('data.custody.user_id')
            ->assertJsonMissingPath('data.custody.external_product_id')
            ->assertJsonMissingPath('data.custody.metadata');
    }

    public function test_customer_cannot_read_another_customers_custody(): void
    {
        $owner = $this->customer();
        $otherCustomer = $this->customer();
        $asset = $this->custodyFor($owner);

        $this->actingAs($otherCustomer, 'sanctum')
            ->getJson("/api/v1/customer/custodies/{$asset->uuid}")
            ->assertNotFound()
            ->assertJsonPath('error.code', 'CUSTODY_NOT_FOUND');
    }

    public function test_delivery_request_requires_an_idempotency_key(): void
    {
        $customer = $this->customer();
        $asset = $this->custodyFor($customer);

        $this->actingAs($customer, 'sanctum')
            ->postJson("/api/v1/customer/custodies/{$asset->uuid}/delivery-request", [])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Idempotency-Key header is required.');
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $customer = $this->customer();
        $asset = $this->custodyFor($customer);

        $this->getJson("/api/v1/customer/custodies/{$asset->uuid}")
            ->assertUnauthorized();
    }

    private function customer(): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate('customer', 'web');

        $user = User::factory()->create();
        $user->assignRole('customer');

        return $user;
    }

    private function custodyFor(User $user): CustodyAsset
    {
        return CustodyAsset::query()->create([
            'user_id' => $user->id,
            'asset_type' => 'gold',
            'title' => 'Test custody asset',
            'quantity' => '1.00000000',
            'weight' => '1.00000000',
            'fineness' => '750.0000',
            'branch_code' => 'TEST',
        ]);
    }
}
