<?php

namespace Tests\Unit\Kimia;

use App\Repositories\Kimia\VoucherRepository;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VoucherRepositoryTest extends TestCase
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
    public function balance_uses_the_swagger_defined_path_without_an_optional_query(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/balance/350' => Http::response([
                [
                    'AccountId' => 350,
                    'Weight' => '13.670',
                    'Money' => '-1065900000',
                    'CurrencyId' => 11,
                    'CurrencySymbol' => 'ریال',
                ],
            ]),
        ]);

        $result = app(VoucherRepository::class)
            ->balance(350);

        $this->assertSame(350, $result[0]['AccountId']);
        $this->assertSame('-1065900000', $result[0]['Money']);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'GET'
                && $request->url() === 'https://kimia.test/api/voucher/balance/350';
        });
    }

    #[Test]
    public function balance_serializes_include_peaks_as_kimia_boolean_literals(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([]),
        ]);

        $repository = app(VoucherRepository::class);

        $repository->balance(350, true);
        $repository->balance(350, false);

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return ($query['includePeaks'] ?? null) === 'true';
        });

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return ($query['includePeaks'] ?? null) === 'false';
        });
    }

    #[Test]
    public function balance_rejects_a_non_positive_account_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(VoucherRepository::class)
            ->balance(0);
    }

    #[Test]
    public function transactions_use_the_swagger_defined_path_and_query_names(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/transactions/350*' => Http::response([
                'PageNumber' => 0,
                'TotalPages' => 1,
                'TotalCount' => 1,
                'Items' => [
                    [
                        'RecordId' => 1000,
                        'Action' => 64,
                        'ActionName' => 'فروش',
                    ],
                ],
            ]),
        ]);

        $result = app(VoucherRepository::class)
            ->transactions(350, 0, 20, true);

        $this->assertSame(64, $result['Items'][0]['Action']);

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return $request->method() === 'GET'
                && str_starts_with(
                    $request->url(),
                    'https://kimia.test/api/voucher/transactions/350?'
                )
                && ($query['pageNumber'] ?? null) === '0'
                && ($query['pageSize'] ?? null) === '20'
                && ($query['descending'] ?? null) === 'true';
        });
    }

    #[Test]
    public function transactions_serialize_false_as_a_kimia_boolean_literal(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/transactions/350*' => Http::response([
                'Items' => [],
            ]),
        ]);

        app(VoucherRepository::class)
            ->transactions(350, 0, 20, false);

        Http::assertSent(function (Request $request): bool {
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return ($query['descending'] ?? null) === 'false';
        });
    }

    #[Test]
    public function transactions_reject_a_negative_page_number(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(VoucherRepository::class)
            ->transactions(350, -1);
    }
}
