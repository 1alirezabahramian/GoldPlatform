<?php

namespace App\Services\Settlement;

use App\Enums\SettlementStatus;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\Settlement;
use App\Services\LedgerService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use LogicException;

class SettlementService
{
    public function __construct(
        private readonly LedgerService $ledgerService
    ) {
    }

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

    public function attachFinancialTransaction(
        Settlement $settlement,
        FinancialTransaction $transaction
    ): Settlement {
        return DB::transaction(function () use ($settlement, $transaction): Settlement {
            $locked = $this->lock($settlement);

            if ($locked->status === SettlementStatus::Completed) {
                throw new LogicException("Completed settlement {$locked->uuid} cannot be relinked.");
            }

            if (! $transaction->exists || $transaction->getKey() === null) {
                throw new LogicException('Settlement requires a persisted financial transaction.');
            }

            if (
                $locked->financial_transaction_id !== null
                && $locked->financial_transaction_id !== $transaction->getKey()
            ) {
                throw new LogicException("Settlement {$locked->uuid} is already linked to another financial transaction.");
            }

            $locked->forceFill([
                'financial_transaction_id' => $transaction->getKey(),
            ])->save();

            return $locked->refresh();
        });
    }

    public function startProcessing(Settlement $settlement): Settlement
    {
        return DB::transaction(function () use ($settlement): Settlement {
            $locked = $this->lock($settlement);

            if ($locked->status === SettlementStatus::Processing) {
                return $locked;
            }

            if ($locked->status !== SettlementStatus::Pending) {
                throw new LogicException("Settlement {$locked->uuid} cannot start from {$locked->status->value}.");
            }

            $locked->forceFill([
                'status' => SettlementStatus::Processing,
                'processing_started_at' => now(),
                'failure_reason' => null,
                'failed_at' => null,
            ])->save();

            return $locked->refresh();
        });
    }

    public function completeWithLedger(
        Settlement $settlement,
        ?string $kimiaReference = null,
        array $metadata = []
    ): Settlement {
        $transaction = $settlement->financialTransaction;

        if ($transaction === null) {
            throw new LogicException("Settlement {$settlement->uuid} has no financial transaction.");
        }

        $this->ledgerService->assertBalanced($transaction);

        throw new LogicException(
            'A balanced internal ledger is audit evidence only and cannot complete a customer financial settlement. Verified Kimia result evidence is required.'
        );
    }

    public function complete(
        Settlement $settlement,
        ?string $kimiaReference = null,
        array $metadata = []
    ): Settlement {
        return DB::transaction(function () use ($settlement, $kimiaReference, $metadata): Settlement {
            $locked = $this->lock($settlement);

            if ($locked->status === SettlementStatus::Completed) {
                return $locked;
            }

            if ($locked->status !== SettlementStatus::Processing) {
                throw new LogicException("Settlement {$locked->uuid} cannot complete from {$locked->status->value}.");
            }

            $locked->forceFill([
                'status' => SettlementStatus::Completed,
                'kimia_reference' => $kimiaReference ?? $locked->kimia_reference,
                'metadata' => array_replace_recursive($locked->metadata ?? [], $metadata),
                'completed_at' => now(),
                'failure_reason' => null,
                'failed_at' => null,
            ])->save();

            return $locked->refresh();
        });
    }

    public function fail(
        Settlement $settlement,
        string $reason,
        array $metadata = []
    ): Settlement {
        return DB::transaction(function () use ($settlement, $reason, $metadata): Settlement {
            $locked = $this->lock($settlement);

            if ($locked->status === SettlementStatus::Failed) {
                return $locked;
            }

            if (! in_array($locked->status, [SettlementStatus::Pending, SettlementStatus::Processing], true)) {
                throw new LogicException("Settlement {$locked->uuid} cannot fail from {$locked->status->value}.");
            }

            $locked->forceFill([
                'status' => SettlementStatus::Failed,
                'failure_reason' => $reason,
                'metadata' => array_replace_recursive($locked->metadata ?? [], $metadata),
                'failed_at' => now(),
                'completed_at' => null,
            ])->save();

            return $locked->refresh();
        });
    }

    private function lock(Settlement $settlement): Settlement
    {
        return Settlement::query()
            ->whereKey($settlement->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }
}
