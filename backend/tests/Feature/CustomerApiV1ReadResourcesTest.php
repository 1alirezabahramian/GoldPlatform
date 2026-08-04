<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerApiV1ReadResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_read_lists_use_the_versioned_envelope_and_pagination_contract(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
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
}
