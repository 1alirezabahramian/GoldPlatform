<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerPanelFinalRegressionTest extends TestCase
{
    #[Test]
    public function customer_v1_contract_foundations_remain_present(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));
        $openApi = (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));
        $response = (string) file_get_contents(app_path('Support/CustomerApiResponse.php'));

        self::assertStringContainsString("Route::prefix('v1/customer')", $routes);
        self::assertStringContainsString('openapi: 3.1.0', $openApi);
        self::assertStringContainsString('default: 25', $openApi);
        self::assertStringContainsString('X-Request-ID', $openApi);
        self::assertStringContainsString("header('X-Request-ID'", $response);
    }

    #[Test]
    public function customer_contract_does_not_expose_kimia_internal_identifiers(): void
    {
        $openApi = (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));

        foreach (['AccountId', 'ProductId', 'Transaction Code', 'Voucher', 'Debit', 'Credit'] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $openApi);
        }
    }
}
