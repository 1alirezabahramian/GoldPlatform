<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\IdempotencyRecord;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class TenantStaffProvisioningTest extends TestCase
{
    use RefreshDatabase;

    private function tenantWithOwner(string $host): array
    {
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('operator', 'web');

        $tenant = Tenant::factory()->create();
        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => $host,
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $owner = User::factory()->create([
            'tenant_id' => $tenant->id,
            'username' => 'owner-'.$tenant->id,
            'is_active' => true,
        ]);
        $owner->assignRole('admin');
        $tenant->forceFill(['owner_user_id' => $owner->id])->save();

        return [$tenant, $owner];
    }

    public function test_explicit_tenant_owner_can_create_operator_in_resolved_tenant(): void
    {
        [$tenant, $owner] = $this->tenantWithOwner('admin.a.test');
        Sanctum::actingAs($owner);

        $response = $this->withHeader('Idempotency-Key', 'staff-create-001')
            ->postJson('https://admin.a.test/api/admin/staff', [
                'name' => 'Operator One',
                'mobile' => '09120000011',
                'username' => 'operator.one',
                'role' => 'operator',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.staff.username', 'operator.one')
            ->assertJsonPath('data.staff.role', 'operator')
            ->assertJsonPath('data.staff.tenant_id', $tenant->id)
            ->assertJsonPath('data.staff.must_change_password', true);

        $temporaryPassword = $response->json('data.temporary_password');
        $this->assertIsString($temporaryPassword);
        $this->assertGreaterThanOrEqual(40, strlen($temporaryPassword));

        $staff = User::query()->where('username', 'operator.one')->firstOrFail();
        $this->assertSame($tenant->id, $staff->tenant_id);
        $this->assertTrue($staff->hasRole('operator'));
        $this->assertTrue($staff->must_change_password);
        $this->assertTrue(Hash::check($temporaryPassword, $staff->password));
        $this->assertNull($staff->wallet, 'Staff provisioning must not create customer wallet/default financial accounts.');

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $owner->id,
            'action' => 'tenant.staff.provisioned',
            'subject_id' => $staff->id,
        ]);
    }

    public function test_normal_admin_is_not_tenant_owner_authority(): void
    {
        [$tenant] = $this->tenantWithOwner('admin.a.test');

        $admin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'username' => 'normal-admin',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->withHeader('Idempotency-Key', 'staff-create-002')
            ->postJson('https://admin.a.test/api/admin/staff', [
                'mobile' => '09120000012',
                'username' => 'should-not-exist',
                'role' => 'operator',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['username' => 'should-not-exist']);
    }

    public function test_client_cannot_select_another_tenant_for_new_staff(): void
    {
        [$tenantA, $owner] = $this->tenantWithOwner('admin.a.test');
        [$tenantB] = $this->tenantWithOwner('admin.b.test');
        Sanctum::actingAs($owner);

        $this->withHeader('Idempotency-Key', 'staff-create-003')
            ->postJson('https://admin.a.test/api/admin/staff', [
                'mobile' => '09120000013',
                'username' => 'tenant-bound',
                'role' => 'admin',
                'tenant_id' => $tenantB->id,
            ])
            ->assertCreated();

        $staff = User::query()->where('username', 'tenant-bound')->firstOrFail();
        $this->assertSame($tenantA->id, $staff->tenant_id);
        $this->assertNotSame($tenantB->id, $staff->tenant_id);
    }

    public function test_idempotency_registry_does_not_persist_or_replay_temporary_password(): void
    {
        [, $owner] = $this->tenantWithOwner('admin.a.test');
        Sanctum::actingAs($owner);

        $payload = [
            'mobile' => '09120000014',
            'username' => 'idempotent.operator',
            'role' => 'operator',
        ];

        $first = $this->withHeader('Idempotency-Key', 'staff-create-004')
            ->postJson('https://admin.a.test/api/admin/staff', $payload)
            ->assertCreated();

        $temporaryPassword = $first->json('data.temporary_password');

        $record = IdempotencyRecord::query()->where('scope', 'staff.create')->firstOrFail();
        $this->assertNull($record->response_body);
        $this->assertStringNotContainsString($temporaryPassword, json_encode($record->toArray()));

        $this->withHeader('Idempotency-Key', 'staff-create-004')
            ->postJson('https://admin.a.test/api/admin/staff', $payload)
            ->assertStatus(409)
            ->assertJsonPath('code', 'IDEMPOTENT_SECRET_RESPONSE_NOT_REPLAYABLE');

        $this->assertSame(1, User::query()->where('username', 'idempotent.operator')->count());
        $this->assertSame(1, AuditLog::query()->where('action', 'tenant.staff.provisioned')->count());
    }
}
