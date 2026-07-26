<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;

class TradeService
{
    public function __construct(
        private FinancialTransactionService $financialTransactionService,
        private LedgerService $ledgerService
    ) {}

    public function execute(Order $order): Trade
    {
        return DB::transaction(function () use ($order) {
            $trade = Trade::create([
                'order_id' => $order->id,
                'trade_no' => $this->generateTradeNo(),
                'quantity' => $order->quantity,
                'unit_price' => $order->unit_price,
                'total_amount' => $order->total_price,
                'status' => 'executed',
                'executed_at' => now(),
            ]);

            $financialTransaction = $this->financialTransactionService->create([
                'reference_type' => Trade::class,
                'reference_id'   => $trade->id,
                'type'           => 'trade',
                'description'    => 'Trade #' . $trade->trade_no,
            ]);

            $trade->update([
                'financial_transaction_id' => $financialTransaction->id,
            ]);

            $this->ledgerService->transfer(
                transaction: $financialTransaction,
                fromAccountId: 1,
                toAccountId: 2,
                amount: $trade->total_amount
            );

            $order->update([
                'status' => 'completed',
            ]);

            return $trade;
        });
    }

    private function generateTradeNo(): string
    {
        return 'TRD-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
    }
}