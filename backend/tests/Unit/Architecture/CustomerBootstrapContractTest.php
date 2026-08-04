<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerBootstrapContractTest extends TestCase
{
    #[Test]
    public function bootstrap_route_is_versioned_authenticated_and_customer_scoped(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));

        self::assertStringContainsString("Route::get('/bootstrap', CustomerBootstrapController::class)", $routes);
        self::assertStringContainsString("Route::prefix('v1/customer')", $routes);
        self::assertStringContainsString("'role:customer'", $routes);
        self::assertStringContainsString("'customer.no-store'", $routes);
    }

    #[Test]
    public function bootstrap_uses_only_existing_code_contracts(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerBootstrapController.php'));

        self::assertStringContainsString('OrderStatus::cases()', $controller);
        self::assertStringContainsString('CustodyStatus::cases()', $controller);
        self::assertStringContainsString('DeliveryStatus::cases()', $controller);
        self::assertStringContainsString('CustomerActivityReadModel::eventTypes()', $controller);
    }

    #[Test]
    public function bootstrap_does_not_publish_financial_or_kimia_rules(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerBootstrapController.php'));

        self::assertStringNotContainsString('fee', $controller);
        self::assertStringNotContainsString('commission', $controller);
        self::assertStringNotContainsString('kimia', strtolower($controller));
        self::assertStringNotContainsString('account_id', $controller);
        self::assertStringNotContainsString('product_id', $controller);
    }

    #[Test]
    public function bootstrap_uses_standard_customer_envelope(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerBootstrapController.php'));

        self::assertStringContainsString('CustomerApiResponse::success', $controller);
        self::assertStringContainsString("'version' => 'v1'", $controller);
        self::assertStringContainsString("'timezone' => 'UTC'", $controller);
    }
}
