<?php

namespace Tests\Feature;

use App\Models\ExternalAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncKimiaAccountsCommandTest extends TestCase
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

    public function test_it_synchronizes_accounts_by_type_without_duplicates(): void
    {
        Http::fakeSequence()
            ->push([[
                'AccountId' => 501,
                'AccountCode' => '1001',
                'Name' => 'مشتری اول',
                'Type' => 1,
                'Mobile' => '09120000000',
                'NationalCode' => '0012345678',
                'IsVisible' => true,
            ]])
            ->push(['data' => [
                [
                    'AccountId' => 501,
                    'AccountCode' => '1001',
                    'Name' => 'مشتری اول ویرایش‌شده',
                    'Type' => 1,
                    'IsVisible' => false,
                ],
                [
                    'AccountId' => 502,
                    'AccountCode' => '1002',
                    'Name' => 'مشتری دوم',
                    'Type' => 3,
                ],
            ]]);

        $this->artisan('kimia:sync-accounts --type=1 --type=3')->assertSuccessful();

        $this->assertDatabaseCount('external_accounts', 2);
        $account = ExternalAccount::query()
            ->where('provider', 'kimia')
            ->where('external_id', 501)
            ->firstOrFail();

        $this->assertSame('مشتری اول ویرایش‌شده', $account->name);
        $this->assertFalse($account->is_active);
        $this->assertSame('synced', $account->sync_status);
        $this->assertNotNull($account->sync_hash);
        $this->assertNotNull($account->last_synced_at);
    }

    public function test_it_updates_an_existing_account_on_the_next_run(): void
    {
        Http::fakeSequence()
            ->push([[
                'AccountId' => 700,
                'AccountCode' => '2001',
                'Name' => 'نام اولیه',
                'Type' => 3,
            ]])
            ->push([[
                'AccountId' => 700,
                'AccountCode' => '2001',
                'Name' => 'نام جدید',
                'Type' => 3,
            ]]);

        $this->artisan('kimia:sync-accounts --type=3')->assertSuccessful();
        $this->artisan('kimia:sync-accounts --type=3')->assertSuccessful();

        $this->assertDatabaseCount('external_accounts', 1);
        $this->assertDatabaseHas('external_accounts', [
            'provider' => 'kimia',
            'external_id' => 700,
            'name' => 'نام جدید',
        ]);
    }
}
