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
                && ($query['descending'] ?? null) === '1';
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
