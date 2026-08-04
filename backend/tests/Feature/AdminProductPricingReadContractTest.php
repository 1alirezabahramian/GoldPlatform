<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class AdminProductPricingReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_read_products_without_inferred_pricing_rules(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoryId = DB::table('product_categories')->insertGetId([
            'title' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'kimia_product_id' => 16,
            'category_id' => $categoryId,
            'title' => 'Test Product',
            'barcode' => null,
            'weight' => '8.000',
            'fineness' => 900,
            'buy_price' => '1000000',
            'sell_price' => '1100000',
            'stock' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/products');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonPath('data.items.0.title', 'Test Product')
            ->assertJsonPath('data.items.0.stored_prices.unit', 'unspecified_in_schema');
    }

    public function test_pricing_overview_reports_unsupported_features_explicitly(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/pricing/overview')
            ->assertOk()
            ->assertJsonPath('data.formula_management_supported', false)
            ->assertJsonPath('data.spread_management_supported', false)
            ->assertJsonPath('data.rounding_management_supported', false)
            ->assertJsonPath('data.dynamic_coin_catalog_supported', false)
            ->assertJsonPath('data.dynamic_currency_catalog_supported', false);
    }

    public function test_operator_cannot_read_admin_products(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->getJson('/api/v1/admin/products')
            ->assertForbidden();
    }

    public function test_product_pagination_is_limited(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/products?per_page=51')
            ->assertUnprocessable();
    }
}
