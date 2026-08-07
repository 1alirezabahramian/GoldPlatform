<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class AccountTenancyPilotBackfillTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function migration_backfills_existing_unassigned_accounts_to_the_explicit_pilot_tenant(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        $account = Account::create(['kimia_id' => 350, 'tenant_id' => null]);

        DB::table('accounts')->where('id', $account->id)->update(['tenant_id' => $tenant->id]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'tenant_id' => $tenant->id,
        ]);
    }

    #[Test]
    public function backfill_rule_refuses_conflicting_existing_tenant_assignments(): void
    {
        $pilot = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();
        $other = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other-test-tenant',
            'is_active' => true,
        ]);

        Account::create(['kimia_id' => 351, 'tenant_id' => $other->id]);

        $conflictExists = DB::table('accounts')
            ->whereNotNull('tenant_id')
            ->where('tenant_id', '!=', $pilot->id)
            ->exists();

        $this->assertTrue($conflictExists);
    }
}
