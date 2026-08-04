<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\CustomerTradingPolicy;
use App\Models\OutboxMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Stages1011CompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_panel_routes_require_authentication_and_roles(): void
    {
        $this->getJson('/api/customer/overview')->assertUnauthorized();

        $user = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $user->assignRole('customer');
        Sanctum::actingAs($user);

        $this->getJson('/api/customer/overview')->assertOk()->assertJsonStructure([
            'balances', 'open_orders', 'custody_count', 'delivery_count',
        ]);
        $this->getJson('/api/admin/audit-logs')->assertForbidden();
    }

    public function test_mutating_routes_require_idempotency_key(): void
    {
        $admin = User::factory()->create();
        Role::findOrCreate('admin', 'web');
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $policy = CustomerTradingPolicy::query()->create([
            'user_group_id' => 1,
            'is_active' => true,
        ]);

        $this->putJson("/api/admin/customer-policies/{$policy->id}", ['is_active' => false])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Idempotency-Key header is required.');
    }

    public function test_policy_change_is_audited_outboxed_and_replayed_once(): void
    {
        $admin = User::factory()->create();
        Role::findOrCreate('admin', 'web');
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $policy = CustomerTradingPolicy::query()->create([
            'user_group_id' => 1,
            'is_active' => true,
        ]);
        $headers = ['Idempotency-Key' => 'policy-change-1'];

        $this->putJson("/api/admin/customer-policies/{$policy->id}", ['is_active' => false], $headers)->assertOk();
        $this->putJson("/api/admin/customer-policies/{$policy->id}", ['is_active' => false], $headers)->assertOk();

        $this->assertFalse($policy->refresh()->is_active);
        $this->assertSame(1, AuditLog::query()->where('action', 'customer_policy.updated')->count());
        $this->assertSame(1, OutboxMessage::query()->where('event_type', 'customer_policy.updated')->count());
    }

    public function test_every_api_response_has_correlation_id(): void
    {
        $this->getJson('/api/user')->assertHeader('X-Request-Id');
    }
}
