<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerCustodyDeliveryContractTest extends TestCase
{
    #[Test]
    public function versioned_routes_use_public_references_and_idempotency(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));

        self::assertStringContainsString("Route::get('/custodies/{reference}'", $routes);
        self::assertStringContainsString("Route::get('/deliveries/{reference}'", $routes);
        self::assertStringContainsString("Route::post('/custodies/{reference}/delivery-request'", $routes);
        self::assertStringContainsString("middleware('idempotency:delivery.request')", $routes);
    }

    #[Test]
    public function customer_queries_enforce_ownership_and_uuid_lookup(): void
    {
        $controller = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerCustodyDeliveryController.php')
        );

        self::assertGreaterThanOrEqual(3, substr_count($controller, "->where('uuid', \$reference)"));
        self::assertGreaterThanOrEqual(3, substr_count($controller, "->where('user_id', \$request->user()->id)"));
        self::assertStringNotContainsString('findOrFail($reference)', $controller);
        self::assertStringNotContainsString('whereKey($reference)', $controller);
    }

    #[Test]
    public function presenter_does_not_expose_internal_or_sensitive_fields(): void
    {
        $presenter = (string) file_get_contents(app_path('Support/CustomerReadPresenter.php'));

        foreach ([
            "'user_id'",
            "'custody_asset_id'",
            "'external_product_id'",
            "'product_code'",
            "'barcode'",
            "'metadata'",
            "'receiver_name'",
            "'receiver_identifier'",
            "'approved_by'",
            "'delivered_by'",
        ] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $presenter);
        }
    }

    #[Test]
    public function delivery_errors_are_customer_safe(): void
    {
        $controller = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerCustodyDeliveryController.php')
        );

        self::assertStringContainsString("'CUSTODY_NOT_FOUND'", $controller);
        self::assertStringContainsString("'DELIVERY_NOT_FOUND'", $controller);
        self::assertStringContainsString("'DELIVERY_NOT_ALLOWED'", $controller);
        self::assertStringNotContainsString('$exception->getMessage()', $controller);
    }
}
