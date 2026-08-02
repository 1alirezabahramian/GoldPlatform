<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KimiaInspectBalanceCommandTest extends TestCase
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
    public function it_reads_and_displays_a_balance_without_mutating_kimia(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                [
                    'AccountId' => 350,
                    'AccountName' => 'Read Only Test',
                    'GroupId' => 3,
                    'Weight' => '13.670',
                    'Money' => '-1065900000',
                    'CurrencyId' => 11,
                    'CurrencySymbol' => 'ریال',
                ],
            ]),
        ]);

        $this->artisan('kimia:inspect-balance', [
            'accountId' => 350,
        ])->assertExitCode(0);

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return $request->method() === 'GET'
                && str_starts_with(
                    $request->url(),
                    'https://kimia.test/api/voucher/balance/350?'
                )
                && ($query['includePeaks'] ?? null) === 'false';
        });

        Http::assertNotSent(function (Request $request): bool {
            return $request->method() !== 'GET';
        });
    }
}
