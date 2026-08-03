<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LedgerService
{
    private const ENTRY_TYPES = ['debit', 'credit'];

    public function createEntry(
        FinancialTransaction $transaction,
        int $walletAccountId,
        string $entryType,
        string $amount,
        string $currency = 'IRR',
        ?string $description = null
    ): LedgerEntry {
        $this->assertPersistedTransaction($transaction);
        $this->assertWalletAccountId($walletAccountId);
        $this->assertEntryType($entryType);
        $this->assertPositiveAmount($amount);
        $this->assertCurrency($currency);

        return LedgerEntry::create([
            'financial_transaction_id' => $transaction->id,
            'wallet_account_id' => $walletAccountId,
            'entry_type' => $entryType,
            'amount' => $amount,
            'currency' => strtoupper($currency),
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
        $this->assertPersistedTransaction($transaction);
        $this->assertWalletAccountId($fromAccountId);
        $this->assertWalletAccountId($toAccountId);
        $this->assertDistinctAccounts($fromAccountId, $toAccountId);
        $this->assertPositiveAmount($amount);
        $this->assertCurrency($currency);

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

    private function assertPersistedTransaction(FinancialTransaction $transaction): void
    {
        if (! $transaction->exists || $transaction->getKey() === null) {
            throw new InvalidArgumentException('Ledger entries require a persisted financial transaction.');
        }
    }

    private function assertWalletAccountId(int $walletAccountId): void
    {
        if ($walletAccountId <= 0) {
            throw new InvalidArgumentException('Wallet account id must be a positive integer.');
        }
    }

    private function assertEntryType(string $entryType): void
    {
        if (! in_array($entryType, self::ENTRY_TYPES, true)) {
            throw new InvalidArgumentException('Ledger entry type must be debit or credit.');
        }
    }

    private function assertPositiveAmount(string $amount): void
    {
        if (! is_numeric($amount) || bccomp($amount, '0', 6) !== 1) {
            throw new InvalidArgumentException('Ledger amount must be greater than zero.');
        }
    }

    private function assertCurrency(string $currency): void
    {
        $normalized = strtoupper(trim($currency));

        if ($normalized === '' || strlen($normalized) > 20) {
            throw new InvalidArgumentException('Ledger currency is required and must not exceed 20 characters.');
        }
    }

    private function assertDistinctAccounts(int $fromAccountId, int $toAccountId): void
    {
        if ($fromAccountId === $toAccountId) {
            throw new InvalidArgumentException('Ledger transfer accounts must be different.');
        }
    }
}
