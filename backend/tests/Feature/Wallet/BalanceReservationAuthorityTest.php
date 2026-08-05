<?php

namespace Tests\Feature\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletAccount;
use App\Services\Wallet\BalanceReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BalanceReservationAuthorityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function reservation_records_workflow_intent_without_using_internal_balance_as_customer_authority(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::query()->firstOrCreate(['user_id' => $user->id]);
        $account = WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => 'RESERVATION-WORKFLOW',
            'title' => 'Reservation Workflow',
            'asset_type' => 'money',
            'unit' => 'IRR',
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);

        $service = app(BalanceReservationService::class);
        $reservation = $service->reserve(
            account: $account,
            amount: '100.00000000',
            idempotencyKey: 'reservation:authority:1'
        );

        $this->assertSame(BalanceReservationService::AUTHORITY, 'workflow_only');
        $this->assertFalse(BalanceReservationService::CUSTOMER_BALANCE_AUTHORITY);
        $this->assertSame('active', $reservation->status);
        $this->assertSame('100.00000000', (string) $reservation->amount);
    }

    #[Test]
    public function reservation_remains_idempotent(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::query()->firstOrCreate(['user_id' => $user->id]);
        $account = WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => 'RESERVATION-IDEMPOTENCY',
            'title' => 'Reservation Idempotency',
            'asset_type' => 'money',
            'unit' => 'IRR',
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);

        $service = app(BalanceReservationService::class);
        $first = $service->reserve($account, '25', 'reservation:authority:2');
        $second = $service->reserve($account, '25', 'reservation:authority:2');

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('balance_reservations', 1);
    }
}
