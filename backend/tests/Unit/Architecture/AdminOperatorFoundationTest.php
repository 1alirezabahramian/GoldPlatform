<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AdminOperatorFoundationTest extends TestCase
{
    #[Test]
    public function versioned_backoffice_routes_are_isolated_and_role_protected(): void
    {
        $routes = (string) file_get_contents(base_path('routes/backoffice_v1.php'));
        $bootstrap = (string) file_get_contents(base_path('bootstrap/app.php'));

        self::assertStringContainsString("prefix('v1/admin')", $routes);
        self::assertStringContainsString("middleware('role:admin')", $routes);
        self::assertStringContainsString("prefix('v1/operator')", $routes);
        self::assertStringContainsString("middleware('role:operator|admin')", $routes);
        self::assertStringContainsString("__DIR__.'/../routes/backoffice_v1.php'", $bootstrap);
    }

    #[Test]
    public function bootstrap_contracts_do_not_expose_kimia_or_financial_write_details(): void
    {
        $admin = (string) file_get_contents(app_path('Http/Controllers/Api/V1/AdminBootstrapController.php'));
        $operator = (string) file_get_contents(app_path('Http/Controllers/Api/V1/OperatorBootstrapController.php'));
        $content = $admin.$operator;

        foreach (['AccountId', 'ProductId', 'TransactionCode', 'VoucherId', 'Debit', 'Credit'] as $internalName) {
            self::assertStringNotContainsString($internalName, $content);
        }

        self::assertStringContainsString("'panel' => 'admin'", $admin);
        self::assertStringContainsString("'panel' => 'operator'", $operator);
    }
}
