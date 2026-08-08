<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\V1\CustomerAssetReadController;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Kimia\AuthenticatedCustomerKimiaBalanceReadService;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerKimiaBalanceIntegrationEntrypointTest extends TestCase
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

    #[Test]
    public function it_reads_only_kimia_balance_for_the_resolved_customer_account(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();
        $account = Account::create(['tenant_id' => $tenant->id, 'kimia_id' => 350]);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'account_id' => $account->id]);

        $context = app(TenantContext::class);
        $context->activate($tenant);

        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                ['CurrencyId' => 11, 'Balance' => '-1250000'],
            ], 200),
        ]);

        $request = Request::create('/api/v1/customer/assets', 'GET');
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('request_id', 'test-request-id');

        $response = app(CustomerAssetReadController::class)->resolvedKimiaBalances(
            $request,
            app(AuthenticatedCustomerKimiaBalanceReadService::class),
            $context,
        );

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('kimia', $payload['data']['source']);
        $this->assertSame('350', $payload['data']['kimia_account_id']);
        $this->assertSame('-1250000', $payload['data']['balances'][0]['Balance']);

        Http::assertSentCount(1);
        Http::assertSent(fn ($sent) => str_starts_with(
            $sent->url(),
            'https://kimia.test/api/voucher/balance/350'
        ));
    }

    #[Test]
    public function it_fails_closed_without_account_binding_and_never_calls_kimia(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'account_id' => null]);

        $context = app(TenantContext::class);
        $context->activate($tenant);

        Http::fake();

        $request = Request::create('/api/v1/customer/assets', 'GET');
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('request_id', 'test-request-id');

        $response = app(CustomerAssetReadController::class)->resolvedKimiaBalances(
            $request,
            app(AuthenticatedCustomerKimiaBalanceReadService::class),
            $context,
        );

        $this->assertSame(503, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('CUSTOMER_ACCOUNT_BINDING_REQUIRED', $payload['code']);

        Http::assertNothingSent();
    }
}
