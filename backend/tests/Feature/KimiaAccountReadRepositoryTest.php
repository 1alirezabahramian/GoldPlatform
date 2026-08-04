<?php

namespace Tests\Feature;

use App\Repositories\Kimia\Read\AccountReadRepository;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KimiaAccountReadRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'test-user');
        config()->set('services.kimia.password', 'test-pass');
        config()->set('services.kimia.book_id', null);

        Http::preventStrayRequests();
    }

    public function test_it_reads_account_groups_with_confirmed_filter_name(): void
    {
        Http::fake([
            'https://kimia.test/api/account/groups*' => Http::response([
                ['AccountGroupId' => 10, 'Name' => 'Retail'],
            ]),
        ]);

        $groups = app(AccountReadRepository::class)->groups(3);

        $this->assertSame(10, $groups[0]['AccountGroupId']);

        Http::assertSent(fn ($request) =>
            $request->method() === 'GET'
            && $request->url() === 'https://kimia.test/api/account/groups?accountType=3'
        );
    }

    public function test_it_reads_retail_accounts_with_type_three(): void
    {
        Http::fake([
            'https://kimia.test/api/account*' => Http::response([
                ['AccountId' => 350, 'Name' => 'Test Account'],
            ]),
        ]);

        $accounts = app(AccountReadRepository::class)->retailAccounts();

        $this->assertSame(350, $accounts[0]['AccountId']);

        Http::assertSent(fn ($request) =>
            $request->method() === 'GET'
            && $request->url() === 'https://kimia.test/api/account?Type=3'
        );
    }

    public function test_it_reads_one_account_without_exposing_write_methods(): void
    {
        Http::fake([
            'https://kimia.test/api/account/350' => Http::response([
                'AccountId' => 350,
                'Name' => 'Test Account',
            ]),
        ]);

        $repository = app(AccountReadRepository::class);
        $account = $repository->find(350);

        $this->assertSame(350, $account['AccountId']);
        $this->assertFalse(method_exists($repository, 'create'));
        $this->assertFalse(method_exists($repository, 'update'));
        $this->assertFalse(method_exists($repository, 'delete'));
    }
}
