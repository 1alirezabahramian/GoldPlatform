<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ExternalAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KimiaInspectAccountReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_classifies_reconciliation_rows_without_mutating_data(): void
    {
        $matched = Account::query()->create([
            'kimia_id' => 501,
            'account_code' => 1001,
            'name' => 'Matched local account',
        ]);

        Account::query()->create([
            'kimia_id' => 502,
            'account_code' => 1002,
            'name' => 'Local only account',
        ]);

        User::factory()->create(['account_id' => $matched->id]);

        ExternalAccount::query()->create([
            'provider' => 'kimia',
            'external_id' => 501,
            'code' => '1001',
            'name' => 'Matched external account',
            'is_active' => true,
        ]);

        ExternalAccount::query()->create([
            'provider' => 'kimia',
            'external_id' => 503,
            'code' => '1003',
            'name' => 'External only account',
            'is_active' => true,
        ]);

        $before = $this->snapshot();

        $this->artisan('kimia:inspect-account-reconciliation --json')
            ->expectsOutputToContain('matched_linked')
            ->expectsOutputToContain('account_only_unlinked')
            ->expectsOutputToContain('external_only')
            ->assertSuccessful();

        $this->assertSame($before, $this->snapshot());
    }

    public function test_it_reports_duplicate_user_binding_as_a_conflict_without_fixing_it(): void
    {
        $account = Account::query()->create([
            'kimia_id' => 700,
            'account_code' => 2001,
            'name' => 'Duplicate binding account',
        ]);

        User::factory()->count(2)->create(['account_id' => $account->id]);

        ExternalAccount::query()->create([
            'provider' => 'kimia',
            'external_id' => 700,
            'code' => '2001',
            'name' => 'Duplicate binding external account',
            'is_active' => true,
        ]);

        $before = $this->snapshot();

        $this->artisan('kimia:inspect-account-reconciliation')
            ->expectsOutputToContain('duplicate_user_binding')
            ->expectsOutputToContain('No data was changed.')
            ->assertSuccessful();

        $this->assertSame($before, $this->snapshot());
        $this->assertSame(2, User::query()->where('account_id', $account->id)->count());
    }

    /** @return array<string, mixed> */
    private function snapshot(): array
    {
        return [
            'accounts' => DB::table('accounts')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
            'external_accounts' => DB::table('external_accounts')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
            'users' => DB::table('users')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
        ];
    }
}
