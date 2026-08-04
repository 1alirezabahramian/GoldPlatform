<?php

namespace App\Services\Settlement;

use App\Enums\SettlementStatus;
use App\Models\Order;
use App\Models\Settlement;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    public function createPending(
        Order $order,
        string $assetType,
        string $amount,
        string $idempotencyKey,
        ?int $tradeId = null,
        ?int $financialTransactionId = null,
        array $metadata = []
    ): Settlement {
        return DB::transaction(function () use (
            $order,
            $assetType,
            $amount,
            $idempotencyKey,
            $tradeId,
            $financialTransactionId,
            $metadata
        ): Settlement {
            $existing = Settlement::query()
                ->where('idempotency_key', $idempotencyKey)
                ->lockForUpdate()
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            try {
                return Settlement::query()->create([
                    'order_id' => $order->id,
                    'trade_id' => $tradeId,
                    'financial_transaction_id' => $financialTransactionId,
                    'status' => SettlementStatus::Pending,
                    'asset_type' => $assetType,
                    'amount' => $amount,
                    'idempotency_key' => $idempotencyKey,
                    'metadata' => $metadata,
                ]);
            } catch (QueryException $exception) {
                $existing = Settlement::query()
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existing !== null) {
                    return $existing;
                }

                throw $exception;
            }
        });
    }
}
