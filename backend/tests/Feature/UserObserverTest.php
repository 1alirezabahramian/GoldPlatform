<?php

namespace Tests\Feature;

use App\Services\Auth\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserObserverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_creates_wallet_and_current_default_accounts(): void
    {
        $user = app(RegistrationService::class)->register([
            'mobile' => '09120000001',
            'password' => 'test-password',
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
        ]);

        $wallet = $user->fresh()->wallet;

        $this->assertNotNull($wallet);
        $this->assertSame(2, $wallet->accounts()->count());

        $this->assertDatabaseHas('wallet_accounts', [
            'wallet_id' => $wallet->id,
            'code' => 'RIAL',
        ]);

        $this->assertDatabaseHas('wallet_accounts', [
            'wallet_id' => $wallet->id,
            'code' => 'GOLD18',
        ]);
    }
}
