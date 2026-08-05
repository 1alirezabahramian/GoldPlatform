<?php

namespace Tests\Architecture;

use Tests\TestCase;

class SettlementLedgerCompletionBoundaryTest extends TestCase
{
    public function test_balanced_internal_ledger_cannot_complete_customer_financial_settlement(): void
    {
        $source = file_get_contents(app_path('Services/Settlement/SettlementService.php'));

        $this->assertIsString($source);
        $this->assertStringContainsString(
            'A balanced internal ledger is audit evidence only',
            $source
        );
        $this->assertStringNotContainsString(
            'return $this->complete($settlement, $kimiaReference, $metadata);',
            $source
        );
    }
}
