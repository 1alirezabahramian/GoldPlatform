<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Wallet\WalletService as UserWalletService;
use App\Services\WalletService as AccountWalletService;
use LogicException;
use Tests\TestCase;

class LegacyWalletMutationBoundaryTest extends TestCase
{
    public function test_user_wallet_service_rejects_local_deposit_and_withdrawal(): void
    {
        $service = app(UserWalletService::class);
        $user = new User();

        foreach (['deposit', 'withdraw'] as $method) {
            try {
                $service->{$method}($user, 'RIAL', '1000');
                $this->fail("{$method} unexpectedly allowed a local financial balance mutation.");
            } catch (LogicException $exception) {
                $this->assertStringContainsString('Kimia is the source of truth', $exception->getMessage());
            }
        }
    }

    public function test_duplicate_account_wallet_service_rejects_local_mutation(): void
    {
        $service = app(AccountWalletService::class);

        foreach (['deposit', 'withdraw'] as $method) {
            try {
                $service->{$method}(1, '1000');
                $this->fail("{$method} unexpectedly allowed a local financial balance mutation.");
            } catch (LogicException $exception) {
                $this->assertStringContainsString('Kimia is the source of truth', $exception->getMessage());
            }
        }
    }
}
