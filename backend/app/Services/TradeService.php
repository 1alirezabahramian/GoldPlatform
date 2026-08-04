<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Trade;
use App\Services\Order\OrderStateMachine;
use App\Services\Settlement\SettlementService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class TradeService
{
    public function __construct(
        private readonly FinancialTransactionService $financialTransactionService,
        private readonly LedgerService $ledgerService,
        private readonly SettlementService $settlementService,
        private readonly OrderStateMachine $orderStateMachine
    ) {
    }

    public function execute(
        Order $order,
        int $fromAccountId,
        int $toAccountId,
        string $ledgerAssetUnit,
        ?string $kimiaReference = null
    ): Trade {
        $this->assertExecutionArguments($fromAccountId, $toAccountId, $ledgerAssetUnit);

        return DB::transaction(function () use (
            $order,
            $fromAccountId,
            $toAccountId,
            $ledgerAssetUnit,
            $kimiaReference
        ): Trade {
            $lockedOrder = Order::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $existingTrade = Trade::query()
                ->where('order_id', $lockedOrder->id)
                ->first();

            if ($existingTrade !== null) {
                if ($lockedOrder->status !== OrderStatus::Completed) {
                    throw new LogicException(
                        "Order {$lockedOrder->id} already has trade {$existingTrade->trade_no} but is not completed."
                    );
                }

                return $existingTrade->refresh();
            }

            if ($lockedOrder->status !== OrderStatus::Approved) {
                throw new LogicException(
                    "Only approved orders can execute; order {$lockedOrder->id} is {$lockedOrder->status->value}."
                );
            }

            $lockedOrder = $this->orderStateMachine->startExecution($lockedOrder);

            $trade = Trade::query()->create([
                'order_id' => $lockedOrder->id,
                'trade_no' => $this->generateTradeNo(),
                'quantity' => (string) $lockedOrder->gold_weight,
                'unit_price' => (string) $lockedOrder->gold_price,
                'total_amount' => (string) $lockedOrder->total_price,
                'status' => 'executing',
                'executed_at' => now(),
            ]);

            $financialTransaction = $this->financialTransactionService->create([
                'reference_type' => Trade::class,
                'reference_id' => $trade->id,
                'type' => 'trade',
                'description' => 'Trade #'.$trade->trade_no,
            ]);

            $trade->forceFill([
                'financial_transaction_id' => $financialTransaction->id,
            ])->save();

            $settlement = $this->settlementService->createPending(
                order: $lockedOrder,
                assetType: strtoupper(trim($ledgerAssetUnit)),
                amount: (string) $trade->total_amount,
                idempotencyKey: 'trade-order-'.$lockedOrder->id,
                tradeId: $trade->id,
                financialTransactionId: $financialTransaction->id,
                metadata: ['trade_no' => $trade->trade_no]
            );

            $settlement = $this->settlementService->startProcessing($settlement);

            $this->ledgerService->transfer(
                transaction: $financialTransaction,
                fromAccountId: $fromAccountId,
                toAccountId: $toAccountId,
                amount: (string) $trade->total_amount,
                currency: $ledgerAssetUnit
            );

            $this->ledgerService->assertBalanced($financialTransaction);
            $lockedOrder = $this->orderStateMachine->startSettlement($lockedOrder);

            $settlement = $this->settlementService->completeWithLedger(
                settlement: $settlement,
                kimiaReference: $kimiaReference,
                metadata: ['ledger_balanced' => true]
            );

            $financialTransaction->forceFill(['status' => 'completed'])->save();
            $trade->forceFill(['status' => 'executed'])->save();
            $this->orderStateMachine->complete($lockedOrder);

            return $trade->refresh();
        });
    }

    private function assertExecutionArguments(
        int $fromAccountId,
        int $toAccountId,
        string $ledgerAssetUnit
    ): void {
        if ($fromAccountId <= 0 || $toAccountId <= 0) {
            throw new InvalidArgumentException('Trading requires explicit positive ledger account ids.');
        }

        if ($fromAccountId === $toAccountId) {
            throw new InvalidArgumentException('Trading ledger accounts must be different.');
        }

        $ledgerAssetUnit = trim($ledgerAssetUnit);

        if ($ledgerAssetUnit === '' || strlen($ledgerAssetUnit) > 20) {
            throw new InvalidArgumentException('Trading requires an explicit ledger asset unit.');
        }
    }

    private function generateTradeNo(): string
    {
        return 'TRD-'.now()->format('YmdHis').'-'.random_int(1000, 9999);
    }
}
