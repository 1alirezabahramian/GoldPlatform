<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerContractRegressionGateTest extends TestCase
{
    #[Test]
    public function openapi_matches_customer_list_query_contracts(): void
    {
        $openapi = (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));
        $request = (string) file_get_contents(app_path('Http/Requests/Api/V1/CustomerPaginationRequest.php'));

        foreach (['Page', 'PerPage', 'Status', 'Sort', 'FromDate', 'ToDate'] as $parameter) {
            self::assertStringContainsString("#/components/parameters/{$parameter}", $openapi);
        }

        self::assertStringContainsString('default: 25', $openapi);
        self::assertStringContainsString("validated('per_page', 25)", $request);
        self::assertStringContainsString('enum: [newest, oldest]', $openapi);
        self::assertStringContainsString('format: date', $openapi);
    }

    #[Test]
    public function openapi_documents_trace_and_no_store_contracts(): void
    {
        $openapi = (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));
        $response = (string) file_get_contents(app_path('Support/CustomerApiResponse.php'));
        $middleware = (string) file_get_contents(app_path('Http/Middleware/CustomerNoStore.php'));

        self::assertStringContainsString('X-Request-ID', $openapi);
        self::assertStringContainsString('Cache-Control', $openapi);
        self::assertStringContainsString("header('X-Request-ID'", $response);
        self::assertStringContainsString("headers->set('Cache-Control'", $middleware);
    }

    #[Test]
    public function customer_contract_remains_free_of_internal_kimia_identifiers(): void
    {
        $openapi = (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));

        foreach (['AccountId', 'ProductId', 'TransactionCode', 'VoucherId'] as $internalName) {
            self::assertStringNotContainsString($internalName, $openapi);
        }
    }
}
