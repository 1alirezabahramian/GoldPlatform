<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class LedgerService
{
    private const ENTRY_TYPES = ['debit', 'credit'];

    public function createEntry(FinancialTransaction $transaction, int $walletAccountId, string $entryType, string $amount, string $currency = 'IRR', ?string $description = null): LedgerEntry
    {
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

    public function transfer(FinancialTransaction $transaction, int $fromAccountId, int $toAccountId, string $amount, string $currency = 'IRR'): void
    {
        $this->assertPersistedTransaction($transaction);
        $this->assertWalletAccountId($fromAccountId);
        $this->assertWalletAccountId($toAccountId);
        $this->assertDistinctAccounts($fromAccountId, $toAccountId);
        $this->assertPositiveAmount($amount);
        $this->assertCurrency($currency);

        DB::transaction(function () use ($transaction, $fromAccountId, $toAccountId, $amount, $currency): void {
            $this->createEntry($transaction, $fromAccountId, 'debit', $amount, $currency, 'Transfer debit');
            $this->createEntry($transaction, $toAccountId, 'credit', $amount, $currency, 'Transfer credit');
        });
    }

    /** @return array<string, array{debit: string, credit: string}> */
    public function totalsByCurrency(FinancialTransaction $transaction): array
    {
        $this->assertPersistedTransaction($transaction);
        $totals = [];

        $transaction->ledgerEntries()->get(['entry_type', 'amount', 'currency'])
            ->each(function (LedgerEntry $entry) use (&$totals): void {
                $currency = strtoupper($entry->currency);
                $totals[$currency] ??= ['debit' => '0.000000', 'credit' => '0.000000'];
                $totals[$currency][$entry->entry_type] = $this->decimalAdd(
                    $totals[$currency][$entry->entry_type],
                    (string) $entry->amount
                );
            });

        return $totals;
    }

    public function isBalanced(FinancialTransaction $transaction): bool
    {
        $totals = $this->totalsByCurrency($transaction);
        if ($totals === []) {
            return false;
        }

        foreach ($totals as $currencyTotals) {
            if ($this->normalizeDecimal($currencyTotals['debit']) !== $this->normalizeDecimal($currencyTotals['credit'])) {
                return false;
            }
        }

        return true;
    }

    public function assertBalanced(FinancialTransaction $transaction): void
    {
        if (! $this->isBalanced($transaction)) {
            throw new LogicException("Financial transaction {$transaction->uuid} must contain balanced debit and credit entries.");
        }
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
        $value = trim($amount);
        if (! preg_match('/^\d+(?:\.\d+)?$/', $value) || ! preg_match('/[1-9]/', $value)) {
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

    private function decimalAdd(string $left, string $right): string
    {
        if (function_exists('bcadd')) {
            return bcadd($left, $right, 6);
        }

        return number_format((float) $left + (float) $right, 6, '.', '');
    }

    private function normalizeDecimal(string $value): string
    {
        $normalized = rtrim(rtrim($value, '0'), '.');
        return $normalized === '' ? '0' : $normalized;
    }
}
