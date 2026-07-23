<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserObserverTest extends TestCase
{
    use RefreshDatabase;

    
    #[\PHPUnit\Framework\Attributes\Test]
public function it_creates_wallet_and_default_accounts_for_new_user(): void
    {
        $user = User::create([
            'mobile' => '09120000001',
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
        ]);

        $wallet = $user->fresh()->wallet;

        $this->assertNotNull($wallet);

        $this->assertEquals(
            9,
            $wallet->accounts()->count()
        );

        $this->assertDatabaseHas('wallet_accounts', [
            'wallet_id' => $wallet->id,
            'code'      => 'RIAL',
        ]);

        $this->assertDatabaseHas('wallet_accounts', [
            'wallet_id' => $wallet->id,
            'code'      => 'GOLD18',
        ]);
    }
}