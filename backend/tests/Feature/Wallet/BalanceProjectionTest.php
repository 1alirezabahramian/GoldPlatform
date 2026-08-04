<?php

namespace Tests\Feature\Wallet;

use App\Models\FinancialTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletAccount;
use App\Services\LedgerService;
use App\Services\Wallet\BalanceProjectionService;
use App\Services\Wallet\BalanceReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BalanceProjectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function wallet_balance_is_rebuilt_from_ledger_and_active_reservations(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::query()->firstOrCreate(['user_id' => $user->id]);
        $source = WalletAccount::query()->create([
            'wallet_id' => $wallet->id, 'code' => 'SRC', 'title' => 'Source',
            'asset_type' => 'money', 'unit' => 'IRR', 'balance' => '0', 'blocked_balance' => '0', 'is_active' => true,
        ]);
        $target = WalletAccount::query()->create([
            'wallet_id' => $wallet->id, 'code' => 'DST', 'title' => 'Target',
            'asset_type' => 'money', 'unit' => 'IRR', 'balance' => '0', 'blocked_balance' => '0', 'is_active' => true,
        ]);
        $transaction = FinancialTransaction::query()->create([
            'reference_type' => User::class, 'reference_id' => $user->id,
            'type' => 'test', 'status' => 'completed',
        ]);

        app(LedgerService::class)->transfer($transaction, $source->id, $target->id, '100.00000000', 'IRR');
        $projection = app(BalanceProjectionService::class);
        $projection->rebuild($target);

        $this->assertSame('100.00000000', $projection->snapshot($target)['total']);

        $reservation = app(BalanceReservationService::class)->reserve($target, '30', 'reservation:test:1');
        $snapshot = $projection->snapshot($target->refresh());
        $this->assertSame('30.00000000', $snapshot['blocked']);
        $this->assertSame('70.00000000', $snapshot['available']);

        app(BalanceReservationService::class)->release($reservation);
        $this->assertSame('100.00000000', $projection->snapshot($target->refresh())['available']);
    }
}
