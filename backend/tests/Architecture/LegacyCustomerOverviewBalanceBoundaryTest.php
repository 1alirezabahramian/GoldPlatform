<?php

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LegacyCustomerOverviewBalanceBoundaryTest extends TestCase
{
    #[Test]
    public function legacy_customer_overview_does_not_expose_internal_wallet_balances(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Api/CustomerPanelController.php'));

        $this->assertStringContainsString("'balance_source' => 'kimia'", $source);
        $this->assertStringContainsString("'balance_error_code' => 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED'", $source);
        $this->assertStringNotContainsString('$account->balance', $source);
        $this->assertStringNotContainsString('$account->blocked_balance', $source);
        $this->assertStringNotContainsString('$account->available_balance', $source);
    }
}
