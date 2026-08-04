<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Trade;
use App\Services\Settlement\SettlementService;
use Illuminate\Support\Facades\DB;
use LogicException;

class TradeService
{
    public function __construct(
        private FinancialTransactionService $financialTransactionService,
        private LedgerService $ledgerService,
        private SettlementService $settlementService,
    ) {}

    public function execute(
        Order $order,
        int $fromAccountId,
        int $toAccountId,
        string $idempotencyKey,
    ): Trade {
        return DB::transaction(function () use (
            $order,
            $fromAccountId,
            $toAccountId,
            $idempotencyKey,
        ): Trade {
            $lockedOrder = Order::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $existingTrade = $lockedOrder->trades()
                ->where('status', 'executed')
                ->first();

            if ($existingTrade !== null) {
                return $existingTrade;
            }

            if ($lockedOrder->status !== 'approved') {
                throw new LogicException(
                    "Order {$lockedOrder->id} cannot execute from status {$lockedOrder->status}."
                );
            }

            $trade = Trade::query()->create([
                'order_id' => $lockedOrder->id,
                'trade_no' => $this->generateTradeNo(),
                'quantity' => $lockedOrder->gold_weight,
                'unit_price' => $lockedOrder->gold_price,
                'total_amount' => $lockedOrder->total_price,
                'status' => 'executed',
                'executed_at' => now(),
            ]);

            $financialTransaction = $this->financialTransactionService->create([
                'reference_type' => Trade::class,
                'reference_id' => $trade->id,
                'type' => 'trade',
                'description' => 'Trade #' . $trade->trade_no,
            ]);

            $trade->forceFill([
                'financial_transaction_id' => $financialTransaction->id,
            ])->save();

            $this->ledgerService->transfer(
                transaction: $financialTransaction,
                fromAccountId: $fromAccountId,
                toAccountId: $toAccountId,
                amount: (string) $trade->total_amount,
                currency: 'IRR',
            );

            $settlement = $this->settlementService->createPending(
                order: $lockedOrder,
                assetType: 'money',
                amount: (string) $trade->total_amount,
                idempotencyKey: $idempotencyKey,
                tradeId: $trade->id,
                financialTransactionId: $financialTransaction->id,
                metadata: [
                    'from_wallet_account_id' => $fromAccountId,
                    'to_wallet_account_id' => $toAccountId,
                ],
            );

            $this->settlementService->startProcessing($settlement);
            $this->settlementService->completeWithLedger(
                settlement: $settlement->refresh(),
                financialTransaction: $financialTransaction,
            );

            $financialTransaction->forceFill([
                'status' => 'completed',
            ])->save();

            $lockedOrder->forceFill([
                'status' => 'completed',
            ])->save();

            return $trade->refresh();
        });
    }

    private function generateTradeNo(): string
    {
        return 'TRD-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
    }
}
