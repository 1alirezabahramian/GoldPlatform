<?php

namespace App\Services\Wallet;

use App\Models\BalanceReservation;
use App\Models\Order;
use App\Models\WalletAccount;
use App\Support\Decimal;
use Illuminate\Support\Facades\DB;
use LogicException;

class BalanceReservationService
{
    public const AUTHORITY = 'workflow_only';

    public const CUSTOMER_BALANCE_AUTHORITY = false;

    public function __construct(private readonly BalanceProjectionService $projection) {}

    public function reserve(
        WalletAccount $account,
        string $amount,
        string $idempotencyKey,
        ?Order $order = null,
        bool $allowNegative = false
    ): BalanceReservation {
        return DB::transaction(function () use ($account, $amount, $idempotencyKey, $order): BalanceReservation {
            $existing = BalanceReservation::query()->where('idempotency_key', $idempotencyKey)->lockForUpdate()->first();
            if ($existing !== null) {
                return $existing;
            }

            $locked = WalletAccount::query()->whereKey($account->getKey())->lockForUpdate()->firstOrFail();
            $amount = Decimal::normalize($amount);
            if (Decimal::compare($amount, '0') <= 0) {
                throw new LogicException('Reservation amount must be positive.');
            }

            // A reservation records workflow intent only. It must never decide whether
            // the customer has sufficient Money, Gold, Coin or Currency. Kimia is the
            // final authority for those balances.
            $reservation = BalanceReservation::query()->create([
                'wallet_account_id' => $locked->id,
                'order_id' => $order?->id,
                'amount' => $amount,
                'status' => 'active',
                'idempotency_key' => $idempotencyKey,
            ]);

            $this->projection->rebuild($locked);

            return $reservation;
        });
    }

    public function release(BalanceReservation $reservation): BalanceReservation
    {
        return $this->finish($reservation, 'released', 'released_at');
    }

    public function consume(BalanceReservation $reservation): BalanceReservation
    {
        return $this->finish($reservation, 'consumed', 'consumed_at');
    }

    private function finish(BalanceReservation $reservation, string $status, string $timestamp): BalanceReservation
    {
        return DB::transaction(function () use ($reservation, $status, $timestamp): BalanceReservation {
            $locked = BalanceReservation::query()->whereKey($reservation->getKey())->lockForUpdate()->firstOrFail();
            if ($locked->status === $status) {
                return $locked;
            }
            if ($locked->status !== 'active') {
                throw new LogicException("Reservation {$locked->uuid} is already {$locked->status}.");
            }

            $locked->forceFill(['status' => $status, $timestamp => now()])->save();
            $this->projection->rebuild($locked->walletAccount);

            return $locked->refresh();
        });
    }
}
