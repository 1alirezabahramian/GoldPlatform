<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\AccountTenancyBackfillPreflightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountTenancyBackfillPreflightTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_fails_closed_for_an_unknown_tenant(): void
    {
        $result = app(AccountTenancyBackfillPreflightService::class)->inspect('missing-tenant');

        $this->assertFalse($result['tenant_found']);
        $this->assertFalse($result['backfill_safe']);
    }

    #[Test]
    public function it_reports_unlinked_accounts_as_unsafe(): void
    {
        Account::create(['kimia_id' => 350]);

        $result = app(AccountTenancyBackfillPreflightService::class)->inspect('khalifeh-coin');

        $this->assertSame(1, $result['total_accounts']);
        $this->assertSame(1, $result['unlinked_accounts']);
        $this->assertFalse($result['backfill_safe']);
    }

    #[Test]
    public function it_reports_safe_only_when_every_account_is_linked_to_the_target_tenant_users(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        $firstAccount = Account::create(['kimia_id' => 350]);
        $secondAccount = Account::create(['kimia_id' => 351]);

        User::factory()->create([
            'mobile' => '09120000350',
            'tenant_id' => $tenant->id,
            'account_id' => $firstAccount->id,
        ]);
        User::factory()->create([
            'mobile' => '09120000351',
            'tenant_id' => $tenant->id,
            'account_id' => $secondAccount->id,
        ]);

        $result = app(AccountTenancyBackfillPreflightService::class)->inspect('khalifeh-coin');

        $this->assertSame(2, $result['total_accounts']);
        $this->assertSame(2, $result['accounts_linked_to_target_tenant_users']);
        $this->assertSame(0, $result['accounts_linked_to_other_tenant_users']);
        $this->assertSame(0, $result['accounts_linked_to_multiple_tenants']);
        $this->assertSame(0, $result['unlinked_accounts']);
        $this->assertTrue($result['backfill_safe']);
    }
}
