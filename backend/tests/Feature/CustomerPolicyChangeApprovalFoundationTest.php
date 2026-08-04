<?php

namespace Tests\Feature;

use App\Models\CustomerTradingPolicy;
use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerPolicyChangeApprovalFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_approved_request_does_not_mutate_active_policy(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $group = UserGroup::query()->create(['title' => 'Test', 'priority' => 1, 'is_active' => true]);
        $policy = CustomerTradingPolicy::query()->create([
            'user_group_id' => $group->id,
            'requires_available_balance' => true,
            'allow_negative_balance' => false,
            'is_active' => true,
        ]);

        $draft = $this->postJson('/api/v1/admin/customer-policy-change-requests', [
            'customer_trading_policy_id' => $policy->id,
            'proposed_changes' => ['allow_negative_balance' => true],
            'reason' => 'Approval workflow test',
        ])->assertOk()->json('data');

        $reference = $draft['reference'];

        $this->postJson("/api/v1/admin/customer-policy-change-requests/{$reference}/submit")
            ->assertOk()
            ->assertJsonPath('data.status', 'submitted');

        $this->postJson("/api/v1/admin/customer-policy-change-requests/{$reference}/approve", [
            'review_note' => 'Approved for later controlled apply.',
        ])->assertOk()->assertJsonPath('data.status', 'approved');

        $this->assertFalse((bool) $policy->fresh()->allow_negative_balance);
    }

    public function test_operator_cannot_access_policy_change_requests(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/customer-policy-change-requests')->assertForbidden();
    }

    public function test_reject_requires_review_note(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $group = UserGroup::query()->create(['title' => 'Test', 'priority' => 1, 'is_active' => true]);
        $policy = CustomerTradingPolicy::query()->create([
            'user_group_id' => $group->id,
            'requires_available_balance' => true,
            'allow_negative_balance' => false,
            'is_active' => true,
        ]);

        $reference = $this->postJson('/api/v1/admin/customer-policy-change-requests', [
            'customer_trading_policy_id' => $policy->id,
            'proposed_changes' => ['asset_lock_minutes' => 60],
            'reason' => 'Test rejection',
        ])->assertOk()->json('data.reference');

        $this->postJson("/api/v1/admin/customer-policy-change-requests/{$reference}/submit")->assertOk();
        $this->postJson("/api/v1/admin/customer-policy-change-requests/{$reference}/reject")
            ->assertUnprocessable();
    }
}
