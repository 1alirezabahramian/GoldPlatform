<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\V1\CustomerAssetReadController;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantResolvedCustomerKimiaBalanceHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'user');
        config()->set('services.kimia.password', 'secret');
        config()->set('services.kimia.read_retries', 0);

        Route::get('/_v2/test-customer-balances', [CustomerAssetReadController::class, 'resolvedKimiaBalances'])
            ->middleware(['auth:sanctum', 'tenant.resolve', 'tenant.user-match']);
    }

    public function test_verified_temporary_host_reads_only_resolved_customer_kimia_balance(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => 'v2-customer-pilot.test',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $account = Account::query()->create([
            'tenant_id' => $tenant->id,
            'kimia_id' => 350,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'account_id' => $account->id,
        ]);

        Sanctum::actingAs($user);

        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                ['CurrencyId' => 11, 'Balance' => '-1250000'],
            ], 200),
        ]);

        $response = $this->getJson('http://v2-customer-pilot.test/_v2/test-customer-balances');

        $response->assertOk()
            ->assertJsonPath('data.source', 'kimia')
            ->assertJsonPath('data.kimia_account_id', '350')
            ->assertJsonPath('data.balances.0.CurrencyId', 11)
            ->assertJsonPath('data.balances.0.Balance', '-1250000');

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => str_starts_with(
            $request->url(),
            'https://kimia.test/api/voucher/balance/350'
        ));
    }

    public function test_cross_tenant_host_fails_closed_before_any_kimia_request(): void
    {
        $pilot = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();
        $other = Tenant::query()->create([
            'name' => 'Other Tenant',
            'slug' => 'other-tenant-v2',
            'is_active' => true,
        ]);

        TenantDomain::query()->create([
            'tenant_id' => $other->id,
            'host' => 'other-v2.test',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $account = Account::query()->create([
            'tenant_id' => $pilot->id,
            'kimia_id' => 350,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $pilot->id,
            'account_id' => $account->id,
        ]);

        Sanctum::actingAs($user);
        Http::fake();

        $response = $this->getJson('http://other-v2.test/_v2/test-customer-balances');

        $response->assertForbidden();

        Http::assertNothingSent();
    }
}
