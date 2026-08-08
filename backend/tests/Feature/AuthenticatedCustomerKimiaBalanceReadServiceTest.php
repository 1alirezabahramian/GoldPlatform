<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Kimia\AuthenticatedCustomerKimiaBalanceReadService;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class AuthenticatedCustomerKimiaBalanceReadServiceTest extends TestCase
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

    public function test_it_does_not_call_kimia_when_customer_binding_is_incomplete(): void
    {
        Http::fake();

        $tenant = Tenant::create(['name' => 'Pilot', 'slug' => 'pilot-incomplete', 'is_active' => true]);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'account_id' => null]);
        $context = new TenantContext();
        $context->activate($tenant);

        $result = app(AuthenticatedCustomerKimiaBalanceReadService::class)->read($user, $context);

        $this->assertFalse($result['resolved']);
        $this->assertSame('CUSTOMER_ACCOUNT_BINDING_REQUIRED', $result['reason']);
        $this->assertNull($result['balances']);
        Http::assertNothingSent();
    }

    public function test_it_reads_balance_only_for_the_resolved_kimia_account(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                ['CurrencyId' => 11, 'Balance' => '-125000'],
            ], 200),
        ]);

        $tenant = Tenant::create(['name' => 'Pilot', 'slug' => 'pilot-resolved', 'is_active' => true]);
        $account = Account::create(['tenant_id' => $tenant->id, 'kimia_id' => 350]);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'account_id' => $account->id]);
        $context = new TenantContext();
        $context->activate($tenant);

        $result = app(AuthenticatedCustomerKimiaBalanceReadService::class)->read($user, $context);

        $this->assertTrue($result['resolved']);
        $this->assertSame('350', $result['kimia_account_id']);
        $this->assertSame('-125000', $result['balances'][0]['Balance']);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://kimia.test/api/voucher/balance/350?includePeaks=false'
        );
    }
}
