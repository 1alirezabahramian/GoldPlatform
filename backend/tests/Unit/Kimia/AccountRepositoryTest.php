<?php

namespace Tests\Unit\Kimia;

use App\Enums\AccountType;
use App\Repositories\Kimia\AccountRepository;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountRepositoryTest extends TestCase
{
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
    public function accounts_use_type_query_parameter(): void
    {
        Http::fake([
            'https://kimia.test/api/account*' => Http::response([
                [
                    'AccountId' => 350,
                    'Type' => AccountType::Retail->value,
                    'Name' => 'Test Account',
                ],
            ]),
        ]);

        $accounts = app(AccountRepository::class)
            ->all(AccountType::Retail->value);

        $this->assertSame(350, $accounts[0]['AccountId']);

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return $request->method() === 'GET'
                && $request->url() !== ''
                && ($query['Type'] ?? null) === '3'
                && ! array_key_exists('accountType', $query);
        });
    }

    #[Test]
    public function account_groups_use_account_type_query_parameter(): void
    {
        Http::fake([
            'https://kimia.test/api/account/groups*' => Http::response([]),
        ]);

        app(AccountRepository::class)
            ->groups(AccountType::Retail->value);

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return ($query['accountType'] ?? null) === '3'
                && ! array_key_exists('Type', $query);
        });
    }
}
