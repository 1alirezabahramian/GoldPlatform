<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class AdminBranchReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_read_branch_code_projection_without_branch_entity(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        DB::table('custody_assets')->insert([
            'uuid' => fake()->uuid(),
            'user_id' => $admin->id,
            'asset_type' => 'gold',
            'title' => 'test',
            'quantity' => 1,
            'weight' => 1,
            'fineness' => 750,
            'branch_code' => 'BR-01',
            'status' => 'in_custody',
            'acquired_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)->getJson('/api/v1/admin/branches')
            ->assertOk()
            ->assertJsonPath('data.branch_entity_supported', false)
            ->assertJsonPath('data.items.0.code', 'BR-01');
    }

    public function test_operator_cannot_read_admin_branches(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)->getJson('/api/v1/admin/branches')->assertForbidden();
    }
}
