<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecoveryCustomerOpenApiIsolationContractTest extends TestCase
{
    #[Test]
    public function openapi_documents_recovered_cp06_and_cp07_paths(): void
    {
        $openApi = (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));

        foreach ([
            '/profile:',
            '/custodies/{reference}:',
            '/custodies/{reference}/delivery-request:',
            '/deliveries/{reference}:',
            'Idempotency-Key',
            'ErrorResponse:',
            'CustomerNotFound:',
        ] as $expected) {
            self::assertStringContainsString($expected, $openApi);
        }

        foreach (['account_id', 'group_id', 'external_product_id', 'kimia_id'] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $openApi);
        }
    }

    #[Test]
    public function current_customer_isolation_is_explicitly_enforced_by_owner(): void
    {
        $controller = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerCustodyDeliveryController.php')
        );

        self::assertGreaterThanOrEqual(3, substr_count($controller, "->where('user_id', \$request->user()->id)"));
        self::assertStringNotContainsString('whereKey($reference)', $controller);
        self::assertStringNotContainsString('findOrFail($reference)', $controller);
    }

    #[Test]
    public function tenant_architecture_is_not_silently_invented_in_customer_slice(): void
    {
        $controller = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerCustodyDeliveryController.php')
        );
        $profile = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerProfileController.php')
        );

        self::assertStringNotContainsString('tenant_id', $controller.$profile);
        self::assertStringNotContainsString('company_id', $controller.$profile);
    }
}
