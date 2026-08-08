<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Integrations\Kimia\Repositories\KimiaAccountRepository;
use App\Integrations\Kimia\Repositories\VoucherRepository;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackendRc1GateTest extends TestCase
{
    use RefreshDatabase;

    private const TENANT_HOST = 'backend-rc1-pilot.test';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia', [
            'base_url' => 'https://kimia.test',
            'username' => 'test-user',
            'password' => 'test-password',
            'timeout' => 5,
        ]);

        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => self::TENANT_HOST,
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);
    }

    #[Test]
    public function panel_permissions_are_isolated_by_role(): void
    {
        Role::findOrCreate('customer', 'web');
        Role::findOrCreate('operator', 'web');
        Role::findOrCreate('admin', 'web');

        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        $customer = User::factory()->create(['tenant_id' => $tenant->id]);
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);
        $this->getJson($this->tenantUrl('/api/customer/overview'))->assertOk();
        $this->getJson($this->tenantUrl('/api/operator/orders/queue'))->assertForbidden();
        $this->getJson($this->tenantUrl('/api/admin/audit-logs'))->assertForbidden();

        $operator = User::factory()->create(['tenant_id' => $tenant->id]);
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);
        $this->getJson($this->tenantUrl('/api/operator/orders/queue'))->assertOk();
        $this->getJson($this->tenantUrl('/api/admin/audit-logs'))->assertForbidden();

        $admin = User::factory()->create(['tenant_id' => $tenant->id]);
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        $this->getJson($this->tenantUrl('/api/admin/audit-logs'))->assertOk();
    }

    #[Test]
    public function kimia_read_only_flow_uses_only_get_requests(): void
    {
        Http::fake([
            'https://kimia.test/api/account*' => Http::response([
                ['AccountId' => 350, 'Type' => AccountType::Retail->value, 'Name' => 'RC1 Account'],
            ]),
            'https://kimia.test/api/voucher/transactions/350*' => Http::response([
                'Items' => [['RecordId' => 1000, 'Action' => 64, 'ActionName' => 'فروش']],
            ]),
        ]);

        $accounts = app(KimiaAccountRepository::class)->all(AccountType::Retail->value);
        $transactions = app(VoucherRepository::class)->transactions(350, 0, 20, true);

        $this->assertSame(350, $accounts[0]->id);
        $this->assertSame(64, $transactions['Items'][0]['Action']);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET');
        Http::assertSentCount(2);
    }

    private function tenantUrl(string $path): string
    {
        return 'http://'.self::TENANT_HOST.$path;
    }
}
