<?php

namespace Tests\Architecture;

use App\Models\User;
use App\Services\Wallet\WalletService;
use LogicException;
use Tests\TestCase;

class InternalWalletMutationBoundaryTest extends TestCase
{
    public function test_internal_customer_financial_mutations_are_disabled(): void
    {
        self::assertFalse(WalletService::CUSTOMER_FINANCIAL_MUTATIONS_ENABLED);

        $service = app(WalletService::class);
        $user = new User();

        try {
            $service->deposit($user, 'RIAL', '1000');
            self::fail('Internal deposit must remain disabled.');
        } catch (LogicException $exception) {
            self::assertStringContainsString('sourced from Kimia', $exception->getMessage());
        }

        try {
            $service->withdraw($user, 'GOLD18', '1.00000000');
            self::fail('Internal withdrawal must remain disabled.');
        } catch (LogicException $exception) {
            self::assertStringContainsString('sourced from Kimia', $exception->getMessage());
        }
    }
}
