<?php

namespace Tests\Unit\Kimia;

use App\Enums\AccountType;
use App\Integrations\Kimia\Repositories\KimiaAccountRepository;
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

        $accounts = app(KimiaAccountRepository::class)
            ->all(AccountType::Retail->value);

        $this->assertSame(350, $accounts[0]->id);
        $this->assertSame('Test Account', $accounts[0]->name);
        $this->assertSame(350, $accounts[0]->rawData['AccountId']);

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return $request->method() === 'GET'
                && ($query['Type'] ?? null) === '3'
                && ! array_key_exists('accountType', $query);
        });
    }

    #[Test]
    public function accounts_accept_wrapped_response_rows(): void
    {
        Http::fake([
            'https://kimia.test/api/account*' => Http::response([
                'data' => [
                    [
                        'AccountId' => 351,
                        'Type' => AccountType::Retail->value,
                        'Name' => 'Wrapped Account',
                        'AccountCode' => 9001,
                    ],
                ],
            ]),
        ]);

        $accounts = app(KimiaAccountRepository::class)->all();

        $this->assertCount(1, $accounts);
        $this->assertSame(351, $accounts[0]->id);
        $this->assertSame(9001, $accounts[0]->code);
    }
}
