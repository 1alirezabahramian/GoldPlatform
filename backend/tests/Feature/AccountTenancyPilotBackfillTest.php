<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $migration = require database_path('migrations/2026_08_08_000400_backfill_existing_accounts_to_khalifeh_pilot_tenant.php');
        $migration->up();

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'tenant_id' => $tenant->id,
        ]);
    }

    #[Test]
    public function migration_refuses_conflicting_existing_tenant_assignments(): void
    {
        $other = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other-test-tenant',
            'is_active' => true,
        ]);

        Account::create(['kimia_id' => 351, 'tenant_id' => $other->id]);

        $migration = require database_path('migrations/2026_08_08_000400_backfill_existing_accounts_to_khalifeh_pilot_tenant.php');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('conflicting tenant assignments');

        $migration->up();
    }
}
