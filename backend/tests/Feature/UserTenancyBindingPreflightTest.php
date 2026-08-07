<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\UserTenancyBindingPreflightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTenancyBindingPreflightTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_reports_current_user_tenancy_and_binding_readiness_without_mutation(): void
    {
        $firstAccount = Account::create(['kimia_id' => 7001]);
        $secondAccount = Account::create(['kimia_id' => 7002]);

        User::factory()->create(['mobile' => '09120000001', 'account_id' => $firstAccount->id]);
        User::factory()->create(['mobile' => '09120000002', 'account_id' => $secondAccount->id]);
        User::factory()->create(['mobile' => '09120000003', 'account_id' => null]);

        $before = User::query()->orderBy('id')->get(['id', 'mobile', 'tenant_id', 'account_id'])->toArray();

        $result = app(UserTenancyBindingPreflightService::class)->inspect();

        $after = User::query()->orderBy('id')->get(['id', 'mobile', 'tenant_id', 'account_id'])->toArray();

        $this->assertSame($before, $after);
        $this->assertTrue($result['tenant_id_column_exists']);
        $this->assertSame(3, $result['total_users']);
        $this->assertSame(2, $result['linked_users']);
        $this->assertSame(1, $result['unlinked_users']);
        $this->assertSame(0, $result['duplicate_account_bindings']);
        $this->assertSame(3, $result['users_missing_tenant_assignment']);
        $this->assertTrue($result['unique_account_binding_preflight_passes']);
        $this->assertFalse($result['authenticated_tenancy_activation_ready']);
    }

    #[Test]
    public function it_reports_duplicate_account_bindings_and_never_repairs_them(): void
    {
        $account = Account::create(['kimia_id' => 7101]);

        User::factory()->create(['mobile' => '09120000011', 'account_id' => $account->id]);
        User::factory()->create(['mobile' => '09120000012', 'account_id' => $account->id]);

        $before = User::query()->orderBy('id')->get(['id', 'tenant_id', 'account_id'])->toArray();

        $result = app(UserTenancyBindingPreflightService::class)->inspect();

        $after = User::query()->orderBy('id')->get(['id', 'tenant_id', 'account_id'])->toArray();

        $this->assertSame($before, $after);
        $this->assertSame(1, $result['duplicate_account_bindings']);
        $this->assertFalse($result['unique_account_binding_preflight_passes']);
    }

    #[Test]
    public function it_reports_authenticated_tenancy_ready_only_after_explicit_assignment(): void
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'is_active' => true,
        ]);

        User::factory()->create([
            'mobile' => '09120000020',
            'tenant_id' => $tenant->id,
            'account_id' => null,
        ]);

        $result = app(UserTenancyBindingPreflightService::class)->inspect();

        $this->assertTrue($result['tenant_id_column_exists']);
        $this->assertSame(0, $result['users_missing_tenant_assignment']);
        $this->assertTrue($result['authenticated_tenancy_activation_ready']);
    }

    #[Test]
    public function command_emits_json_and_does_not_infer_tenant_assignment(): void
    {
        User::factory()->create(['mobile' => '09120000021', 'account_id' => null]);

        $exitCode = Artisan::call('tenancy:inspect-user-binding-readiness', ['--json' => true]);
        $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(0, $exitCode);
        $this->assertTrue($result['tenant_id_column_exists']);
        $this->assertSame(1, $result['users_missing_tenant_assignment']);
        $this->assertFalse($result['authenticated_tenancy_activation_ready']);
    }
}
