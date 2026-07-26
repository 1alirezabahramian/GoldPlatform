<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    public function createEntry(
        FinancialTransaction $transaction,
        int $walletAccountId,
        string $entryType,
        string $amount,
        string $currency = 'IRR',
        ?string $description = null
    ): LedgerEntry {
        return LedgerEntry::create([
            'financial_transaction_id' => $transaction->id,
            'wallet_account_id' => $walletAccountId,
            'entry_type' => $entryType,
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
        ]);
    }

    public function transfer(
        FinancialTransaction $transaction,
        int $fromAccountId,
        int $toAccountId,
        string $amount,
        string $currency = 'IRR'
    ): void {
        DB::transaction(function () use (
            $transaction,
            $fromAccountId,
            $toAccountId,
            $amount,
            $currency
        ) {
            $this->createEntry(
                transaction: $transaction,
                walletAccountId: $fromAccountId,
                entryType: 'debit',
                amount: $amount,
                currency: $currency,
                description: 'Transfer debit'
            );

            $this->createEntry(
                transaction: $transaction,
                walletAccountId: $toAccountId,
                entryType: 'credit',
                amount: $amount,
                currency: $currency,
                description: 'Transfer credit'
            );
        });
    }
}