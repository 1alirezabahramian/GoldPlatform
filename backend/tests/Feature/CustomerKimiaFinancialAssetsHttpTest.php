<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerKimiaFinancialAssetsHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'user');
        config()->set('services.kimia.password', 'secret');
        config()->set('services.kimia.read_retries', 0);
    }

    public function test_verified_host_returns_customer_safe_kimia_financial_assets(): void
    {
        [$tenant, $user] = $this->boundCustomer('v2-customer-pilot.test');

        Sanctum::actingAs($user);
        $this->fakeRealShapedKimiaReads();

        $response = $this->getJson('http://v2-customer-pilot.test/api/v1/customer/assets')
            ->assertOk()
            ->assertHeader('X-Request-Id')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('data.source', 'kimia')
            ->assertJsonPath('data.money.amount_toman', '-299921991.4')
            ->assertJsonPath('data.money.unit', 'toman')
            ->assertJsonPath('data.gold.weight_gram', '1')
            ->assertJsonPath('data.gold.unit', 'gram')
            ->assertJsonPath('data.coins.0.name', 'سکه امامی')
            ->assertJsonPath('data.coins.0.quantity', '1')
            ->assertJsonPath('data.currencies.0.name', 'دلار')
            ->assertJsonPath('data.currencies.0.amount', '500');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        foreach (['kimia_account_id', 'AccountId', 'CurrencyId', 'CoinId', 'GroupId'] as $internalField) {
            $this->assertStringNotContainsString($internalField, $encoded);
        }

        Http::assertSentCount(3);
    }

    public function test_incomplete_binding_fails_closed_before_any_kimia_request(): void
    {
        $tenant = $this->tenantForHost('v2-incomplete.test');
        $user = $this->customer([
            'tenant_id' => $tenant->id,
            'account_id' => null,
        ]);

        Sanctum::actingAs($user);
        Http::fake();

        $this->getJson('http://v2-incomplete.test/api/v1/customer/assets')
            ->assertServiceUnavailable()
            ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_UNAVAILABLE')
            ->assertJsonMissingPath('data');

        Http::assertNothingSent();
    }

    public function test_cross_tenant_host_fails_closed_before_any_kimia_request(): void
    {
        [$pilot, $user] = $this->boundCustomer('pilot-v2.test');
        $other = Tenant::query()->create([
            'name' => 'Other Tenant',
            'slug' => 'other-tenant-public-assets',
            'is_active' => true,
        ]);

        TenantDomain::query()->create([
            'tenant_id' => $other->id,
            'host' => 'other-v2.test',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $this->assertNotSame($pilot->id, $other->id);

        Sanctum::actingAs($user);
        Http::fake();

        $this->getJson('http://other-v2.test/api/v1/customer/assets')->assertForbidden();

        Http::assertNothingSent();
    }

    public function test_ambiguous_coin_currency_identity_fails_closed(): void
    {
        [, $user] = $this->boundCustomer('v2-ambiguous.test');

        Sanctum::actingAs($user);
        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                ['Weight' => 1, 'Money' => -2999219914, 'CurrencyId' => 11, 'CurrencySymbol' => 'ریال'],
                ['Money' => 2, 'CurrencyId' => 77, 'CurrencySymbol' => 'ambiguous'],
            ], 200),
            'https://kimia.test/api/product/coins*' => Http::response([
                ['CoinId' => 77, 'Name' => 'Coin 77', 'IsVisible' => true],
            ], 200),
            'https://kimia.test/api/product/currencies*' => Http::response([
                ['CurrencyId' => 77, 'Name' => 'Currency 77', 'IsVisible' => true],
            ], 200),
        ]);

        $this->getJson('http://v2-ambiguous.test/api/v1/customer/assets')
            ->assertServiceUnavailable()
            ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_UNAVAILABLE');

        Http::assertSentCount(3);
    }

    /** @return array{Tenant, User} */
    private function boundCustomer(string $host): array
    {
        $tenant = $this->tenantForHost($host);
        $account = Account::query()->create([
            'tenant_id' => $tenant->id,
            'kimia_id' => 350,
        ]);
        $user = $this->customer([
            'tenant_id' => $tenant->id,
            'account_id' => $account->id,
        ]);

        return [$tenant, $user];
    }

    private function tenantForHost(string $host): Tenant
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => $host,
            'is_primary' => false,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        return $tenant;
    }

    /** @param array<string, mixed> $attributes */
    private function customer(array $attributes): User
    {
        Role::findOrCreate('customer', 'web');
        $user = User::factory()->create($attributes);
        $user->assignRole('customer');

        return $user;
    }

    private function fakeRealShapedKimiaReads(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                ['Weight' => 1, 'Money' => -2999219914, 'CurrencyId' => 11, 'CurrencySymbol' => 'ریال'],
                ['Weight' => 0, 'Money' => 500, 'CurrencyId' => 12, 'CurrencySymbol' => '$'],
                ['Money' => 1, 'CurrencyId' => 16, 'CurrencySymbol' => 'امامی'],
            ], 200),
            'https://kimia.test/api/product/coins*' => Http::response([
                ['CoinId' => 16, 'Name' => 'سکه امامی', 'IsVisible' => true],
            ], 200),
            'https://kimia.test/api/product/currencies*' => Http::response([
                ['CurrencyId' => 12, 'Name' => 'دلار', 'IsVisible' => true],
            ], 200),
        ]);
    }
}
