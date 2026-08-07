<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Services\Tenancy\UserTenancyBindingPreflightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $first = User::factory()->create(['mobile' => '09120000001', 'account_id' => $firstAccount->id]);
        $second = User::factory()->create(['mobile' => '09120000002', 'account_id' => $secondAccount->id]);
        $third = User::factory()->create(['mobile' => '09120000003', 'account_id' => null]);

        $before = User::query()->orderBy('id')->get(['id', 'mobile', 'account_id'])->toArray();

        $result = app(UserTenancyBindingPreflightService::class)->inspect();

        $after = User::query()->orderBy('id')->get(['id', 'mobile', 'account_id'])->toArray();

        $this->assertSame($before, $after);
        $this->assertFalse($result['tenant_id_column_exists']);
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

        $before = User::query()->orderBy('id')->pluck('account_id', 'id')->all();

        $result = app(UserTenancyBindingPreflightService::class)->inspect();

        $after = User::query()->orderBy('id')->pluck('account_id', 'id')->all();

        $this->assertSame($before, $after);
        $this->assertSame(1, $result['duplicate_account_bindings']);
        $this->assertFalse($result['unique_account_binding_preflight_passes']);
    }

    #[Test]
    public function command_emits_json_and_does_not_infer_tenant_assignment(): void
    {
        User::factory()->create(['mobile' => '09120000021', 'account_id' => null]);

        $this->artisan('tenancy:inspect-user-binding-readiness', ['--json' => true])
            ->expectsOutputToContain('"tenant_id_column_exists": false')
            ->expectsOutputToContain('"users_missing_tenant_assignment": 1')
            ->assertSuccessful();
    }
}
