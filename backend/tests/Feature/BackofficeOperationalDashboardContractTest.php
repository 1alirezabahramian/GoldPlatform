<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class BackofficeOperationalDashboardContractTest extends TestCase
{
    #[Test]
    public function dashboard_contract_does_not_publish_unconfirmed_financial_metrics(): void
    {
        $readModel = (string) file_get_contents(app_path('ReadModels/BackofficeOperationalDashboard.php'));

        self::assertStringContainsString("'financial_metrics_supported' => false", $readModel);

        foreach (['revenue', 'profit', 'gold_value', 'kimia_reference', 'receiver_identifier'] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $readModel);
        }
    }

    #[Test]
    public function dashboard_routes_are_versioned_and_role_protected(): void
    {
        $routes = (string) file_get_contents(base_path('routes/backoffice_v1.php'));

        self::assertStringContainsString("Route::get('/dashboard', AdminOperationalDashboardController::class)", $routes);
        self::assertStringContainsString("Route::get('/dashboard', OperatorOperationalDashboardController::class)", $routes);
    }
}
