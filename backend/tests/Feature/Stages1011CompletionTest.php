<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\CustodyAsset;
use App\Models\CustomerTradingPolicy;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\OutboxMessage;
use App\Models\User;
use App\Models\UserGroup;
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
        $admin = $this->actingAdmin();
        $policy = $this->policy();

        $this->putJson("/api/admin/customer-policies/{$policy->id}", ['is_active' => false])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Idempotency-Key header is required.');
    }

    public function test_financial_policy_change_fails_closed_without_side_effects(): void
    {
        $admin = $this->actingAdmin();
        $policy = $this->policy();
        $headers = ['Idempotency-Key' => 'policy-change-1'];

        $this->putJson("/api/admin/customer-policies/{$policy->id}", ['is_active' => false], $headers)
            ->assertStatus(503)
            ->assertJsonPath('code', 'FINANCIAL_POLICY_GROUND_TRUTH_REQUIRED');

        $this->putJson("/api/admin/customer-policies/{$policy->id}", ['is_active' => false], $headers)
            ->assertStatus(503)
            ->assertJsonPath('code', 'FINANCIAL_POLICY_GROUND_TRUTH_REQUIRED');

        $this->assertTrue($policy->refresh()->is_active);
        $this->assertSame(0, AuditLog::query()->where('action', 'customer_policy.updated')->count());
        $this->assertSame(0, OutboxMessage::query()->where('event_type', 'customer_policy.updated')->count());
    }

    public function test_admin_observability_responses_redact_sensitive_payloads(): void
    {
        $admin = $this->actingAdmin();

        AuditLog::query()->create([
            'actor_id' => $admin->id,
            'action' => 'security.redaction.test',
            'subject_type' => User::class,
            'subject_id' => (string) $admin->id,
            'request_id' => '00000000-0000-4000-8000-000000000001',
            'ip_address' => '203.0.113.10',
            'user_agent' => 'Sensitive test user agent',
            'before' => ['secret' => 'before-secret'],
            'after' => ['secret' => 'after-secret'],
            'metadata' => ['token' => 'metadata-secret'],
        ]);

        OutboxMessage::query()->create([
            'event_type' => 'security.redaction.test',
            'aggregate_type' => User::class,
            'aggregate_id' => (string) $admin->id,
            'payload' => ['token' => 'payload-secret'],
            'last_error' => 'internal-error-details',
        ]);

        $auditResponse = $this->getJson('/api/admin/audit-logs')
            ->assertOk()
            ->assertJsonPath('data.0.action', 'security.redaction.test');

        $outboxResponse = $this->getJson('/api/admin/outbox')
            ->assertOk()
            ->assertJsonPath('data.0.event_type', 'security.redaction.test');

        foreach ([$auditResponse, $outboxResponse] as $response) {
            $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
            $this->assertStringNotContainsString('before-secret', $encoded);
            $this->assertStringNotContainsString('after-secret', $encoded);
            $this->assertStringNotContainsString('metadata-secret', $encoded);
            $this->assertStringNotContainsString('payload-secret', $encoded);
            $this->assertStringNotContainsString('internal-error-details', $encoded);
            $this->assertStringNotContainsString('203.0.113.10', $encoded);
            $this->assertStringNotContainsString('Sensitive test user agent', $encoded);
        }
    }

    public function test_operator_queue_responses_redact_sensitive_fields(): void
    {
        $operator = $this->actingOperator();
        $customer = User::factory()->create();

        Order::query()->create([
            'user_id' => $customer->id,
            'type' => 'buy',
            'gold_weight' => '1.250',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1250000',
            'description' => 'sensitive-order-description',
            'status_reason' => 'sensitive-order-reason',
            'status' => 'pending',
        ]);

        $custody = CustodyAsset::query()->create([
            'user_id' => $customer->id,
            'asset_type' => 'gold',
            'title' => 'Operator queue test custody',
            'quantity' => '1.00000000',
            'weight' => '1.00000000',
            'fineness' => '750.0000',
            'branch_code' => 'TEST',
        ]);

        DeliveryRequest::query()->create([
            'custody_asset_id' => $custody->id,
            'user_id' => $customer->id,
            'branch_code' => 'TEST',
            'status' => 'requested',
            'receiver_name' => 'Sensitive Receiver',
            'receiver_identifier' => 'sensitive-receiver-identifier',
            'metadata' => ['token' => 'sensitive-delivery-metadata'],
        ]);

        $orderResponse = $this->getJson('/api/operator/orders/queue')
            ->assertOk()
            ->assertJsonPath('data.0.status', 'pending');

        $deliveryResponse = $this->getJson('/api/operator/deliveries/queue')
            ->assertOk()
            ->assertJsonPath('data.0.status', 'requested');

        foreach ([$orderResponse, $deliveryResponse] as $response) {
            $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
            $this->assertStringNotContainsString('sensitive-order-description', $encoded);
            $this->assertStringNotContainsString('sensitive-order-reason', $encoded);
            $this->assertStringNotContainsString('Sensitive Receiver', $encoded);
            $this->assertStringNotContainsString('sensitive-receiver-identifier', $encoded);
            $this->assertStringNotContainsString('sensitive-delivery-metadata', $encoded);
        }
    }

    public function test_every_api_response_has_correlation_id(): void
    {
        $this->getJson('/api/user')->assertHeader('X-Request-Id');
    }

    private function actingAdmin(): User
    {
        $admin = User::factory()->create();
        Role::findOrCreate('admin', 'web');
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        return $admin;
    }

    private function actingOperator(): User
    {
        $operator = User::factory()->create();
        Role::findOrCreate('operator', 'web');
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);
        return $operator;
    }

    private function policy(): CustomerTradingPolicy
    {
        $group = UserGroup::query()->create(['title' => 'test group']);
        return CustomerTradingPolicy::query()->create([
            'user_group_id' => $group->id,
            'is_active' => true,
        ]);
    }
}
