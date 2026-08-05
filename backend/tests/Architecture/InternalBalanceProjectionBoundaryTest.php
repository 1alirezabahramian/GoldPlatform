<?php

namespace Tests\Architecture;

use App\Services\Wallet\BalanceProjectionService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InternalBalanceProjectionBoundaryTest extends TestCase
{
    #[Test]
    public function ledger_projection_is_explicitly_not_a_customer_balance_source(): void
    {
        self::assertSame(
            'audit_reconciliation_only',
            BalanceProjectionService::PURPOSE,
        );

        self::assertFalse(BalanceProjectionService::CUSTOMER_BALANCE_SOURCE);
    }
}
