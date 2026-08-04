<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminReportCatalogReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_read_report_catalog_without_unsupported_financial_claims(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/reports/catalog');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonPath('data.exports.formats.xlsx.supported', false)
            ->assertJsonPath('data.exports.formats.pdf.supported', false)
            ->assertJsonFragment(['key' => 'orders', 'supported' => true])
            ->assertJsonFragment(['key' => 'revenue', 'supported' => false]);
    }

    public function test_operator_cannot_read_report_catalog(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->getJson('/api/v1/admin/reports/catalog')
            ->assertForbidden();
    }
}
