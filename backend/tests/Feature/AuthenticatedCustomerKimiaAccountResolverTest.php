<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Kimia\AuthenticatedCustomerKimiaAccountResolver;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticatedCustomerKimiaAccountResolverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_fails_closed_without_tenant_context(): void
    {
        $user = User::factory()->create(['tenant_id' => null, 'account_id' => null]);
        $context = new TenantContext();

        $result = app(AuthenticatedCustomerKimiaAccountResolver::class)->resolve($user, $context);

        $this->assertFalse($result['resolved']);
        $this->assertSame('TENANT_CONTEXT_REQUIRED', $result['reason']);
        $this->assertNull($result['kimia_account_id']);
    }

    #[Test]
    public function it_fails_closed_when_user_tenant_does_not_match_active_tenant(): void
    {
        $firstTenant = Tenant::create(['name' => 'First', 'slug' => 'first', 'is_active' => true]);
        $secondTenant = Tenant::create(['name' => 'Second', 'slug' => 'second', 'is_active' => true]);
        $user = User::factory()->create(['tenant_id' => $firstTenant->id, 'account_id' => null]);
        $context = new TenantContext();
        $context->activate($secondTenant);

        $result = app(AuthenticatedCustomerKimiaAccountResolver::class)->resolve($user, $context);

        $this->assertFalse($result['resolved']);
        $this->assertSame('USER_TENANT_MISMATCH', $result['reason']);
    }

    #[Test]
    public function it_fails_closed_when_account_has_no_tenant_assignment(): void
    {
        $tenant = Tenant::create(['name' => 'Khalifeh', 'slug' => 'khalifeh-test', 'is_active' => true]);
        $account = Account::create(['kimia_id' => 350]);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'account_id' => $account->id]);
        $context = new TenantContext();
        $context->activate($tenant);

        $result = app(AuthenticatedCustomerKimiaAccountResolver::class)->resolve($user, $context);

        $this->assertFalse($result['resolved']);
        $this->assertSame('ACCOUNT_TENANT_OWNERSHIP_REQUIRED', $result['reason']);
        $this->assertNull($result['kimia_account_id']);
    }

    #[Test]
    public function it_fails_closed_when_account_tenant_does_not_match_active_tenant(): void
    {
        $firstTenant = Tenant::create(['name' => 'First', 'slug' => 'first-account', 'is_active' => true]);
        $secondTenant = Tenant::create(['name' => 'Second', 'slug' => 'second-account', 'is_active' => true]);
        $account = Account::create(['tenant_id' => $firstTenant->id, 'kimia_id' => 350]);
        $user = User::factory()->create(['tenant_id' => $secondTenant->id, 'account_id' => $account->id]);
        $context = new TenantContext();
        $context->activate($secondTenant);

        $result = app(AuthenticatedCustomerKimiaAccountResolver::class)->resolve($user, $context);

        $this->assertFalse($result['resolved']);
        $this->assertSame('ACCOUNT_TENANT_MISMATCH', $result['reason']);
        $this->assertNull($result['kimia_account_id']);
    }

    #[Test]
    public function it_resolves_only_when_tenant_user_account_and_kimia_identity_match(): void
    {
        $tenant = Tenant::create(['name' => 'Khalifeh', 'slug' => 'khalifeh-resolved', 'is_active' => true]);
        $account = Account::create(['tenant_id' => $tenant->id, 'kimia_id' => 350]);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'account_id' => $account->id]);
        $context = new TenantContext();
        $context->activate($tenant);

        $result = app(AuthenticatedCustomerKimiaAccountResolver::class)->resolve($user, $context);

        $this->assertTrue($result['resolved']);
        $this->assertSame('RESOLVED', $result['reason']);
        $this->assertSame('350', $result['kimia_account_id']);
    }
}
