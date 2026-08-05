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
    public function registration_creates_the_user_without_local_financial_balances(): void
    {
        $user = app(RegistrationService::class)->register([
            'mobile' => '09120000001',
            'password' => 'test-password',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'mobile' => '09120000001',
        ]);

        $this->assertDatabaseMissing('wallets', [
            'user_id' => $user->id,
        ]);

        $this->assertNull($user->fresh()->wallet);
        $this->assertDatabaseCount('wallet_accounts', 0);
    }
}
