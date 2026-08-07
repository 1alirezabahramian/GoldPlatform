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
    public function it_fails_closed_until_account_tenant_ownership_is_proven(): void
    {
        $tenant = Tenant::create(['name' => 'Khalifeh', 'slug' => 'khalifeh-test', 'is_active' => true]);
        $account = Account::create(['kimia_id' => 350]);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'account_id' => $account->id]);
        $context = new TenantContext();
        $context->activate($tenant);

        $result = app(AuthenticatedCustomerKimiaAccountResolver::class)->resolve($user, $context);

        $this->assertFalse($result['resolved']);
        $this->assertSame('ACCOUNT_TENANT_OWNERSHIP_NOT_PROVEN', $result['reason']);
        $this->assertNull($result['kimia_account_id']);
    }
}
