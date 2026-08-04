<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Integrations\Kimia\Repositories\KimiaAccountRepository;
use App\Integrations\Kimia\Repositories\VoucherRepository;
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

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia', [
            'base_url' => 'https://kimia.test',
            'username' => 'test-user',
            'password' => 'test-password',
            'timeout' => 5,
        ]);
    }

    #[Test]
    public function panel_permissions_are_isolated_by_role(): void
    {
        Role::findOrCreate('customer', 'web');
        Role::findOrCreate('operator', 'web');
        Role::findOrCreate('admin', 'web');

        $customer = User::factory()->create();
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);
        $this->getJson('/api/customer/overview')->assertOk();
        $this->getJson('/api/operator/orders/queue')->assertForbidden();
        $this->getJson('/api/admin/audit-logs')->assertForbidden();

        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);
        $this->getJson('/api/operator/orders/queue')->assertOk();
        $this->getJson('/api/admin/audit-logs')->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        $this->getJson('/api/admin/audit-logs')->assertOk();
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
}
