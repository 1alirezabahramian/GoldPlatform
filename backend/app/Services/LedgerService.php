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
        $amount = $this->normalizePositiveDecimal($amount);
        $currency = $this->normalizeCurrency($currency);

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
        $this->assertPersistedTransaction($transaction);
        $this->assertWalletAccountId($fromAccountId);
        $this->assertWalletAccountId($toAccountId);
        $this->assertDistinctAccounts($fromAccountId, $toAccountId);
        $amount = $this->normalizePositiveDecimal($amount);
        $currency = $this->normalizeCurrency($currency);

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

        $transaction->ledgerEntries()
            ->get(['entry_type', 'amount', 'currency'])
            ->each(function (LedgerEntry $entry) use (&$totals): void {
                $currency = $this->normalizeCurrency((string) $entry->currency);
                $totals[$currency] ??= ['debit' => '0', 'credit' => '0'];
                $totals[$currency][$entry->entry_type] = $this->addDecimals(
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
            if ($this->canonicalDecimal($currencyTotals['debit']) !== $this->canonicalDecimal($currencyTotals['credit'])) {
                return false;
            }
        }

        return true;
    }

    public function assertBalanced(FinancialTransaction $transaction): void
    {
        if (! $this->isBalanced($transaction)) {
            throw new LogicException(
                "Financial transaction {$transaction->uuid} must contain balanced debit and credit entries for every asset unit."
            );
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

    private function normalizePositiveDecimal(string $amount): string
    {
        $amount = trim($amount);

        if (! preg_match('/^\d+(?:\.\d+)?$/', $amount) || ! preg_match('/[1-9]/', $amount)) {
            throw new InvalidArgumentException('Ledger amount must be a positive decimal string.');
        }

        return $this->canonicalDecimal($amount);
    }

    private function normalizeCurrency(string $currency): string
    {
        $normalized = strtoupper(trim($currency));

        if ($normalized === '' || strlen($normalized) > 20) {
            throw new InvalidArgumentException('Ledger asset unit is required and must not exceed 20 characters.');
        }

        return $normalized;
    }

    private function assertDistinctAccounts(int $fromAccountId, int $toAccountId): void
    {
        if ($fromAccountId === $toAccountId) {
            throw new InvalidArgumentException('Ledger transfer accounts must be different.');
        }
    }

    private function addDecimals(string $left, string $right): string
    {
        [$leftWhole, $leftFraction] = $this->splitDecimal($left);
        [$rightWhole, $rightFraction] = $this->splitDecimal($right);
        $scale = max(strlen($leftFraction), strlen($rightFraction));

        $leftDigits = ltrim($leftWhole.str_pad($leftFraction, $scale, '0'), '0') ?: '0';
        $rightDigits = ltrim($rightWhole.str_pad($rightFraction, $scale, '0'), '0') ?: '0';
        $sum = '';
        $carry = 0;
        $maxLength = max(strlen($leftDigits), strlen($rightDigits));
        $leftDigits = str_pad($leftDigits, $maxLength, '0', STR_PAD_LEFT);
        $rightDigits = str_pad($rightDigits, $maxLength, '0', STR_PAD_LEFT);

        for ($index = $maxLength - 1; $index >= 0; $index--) {
            $digit = (int) $leftDigits[$index] + (int) $rightDigits[$index] + $carry;
            $sum = (string) ($digit % 10).$sum;
            $carry = intdiv($digit, 10);
        }

        if ($carry > 0) {
            $sum = (string) $carry.$sum;
        }

        if ($scale === 0) {
            return $this->canonicalDecimal($sum);
        }

        $sum = str_pad($sum, $scale + 1, '0', STR_PAD_LEFT);

        return $this->canonicalDecimal(
            substr($sum, 0, -$scale).'.'.substr($sum, -$scale)
        );
    }

    /** @return array{0:string,1:string} */
    private function splitDecimal(string $value): array
    {
        $value = $this->canonicalDecimal($value);
        $parts = explode('.', $value, 2);

        return [$parts[0], $parts[1] ?? ''];
    }

    private function canonicalDecimal(string $value): string
    {
        $value = trim($value);
        $parts = explode('.', $value, 2);
        $whole = ltrim($parts[0], '0');
        $whole = $whole === '' ? '0' : $whole;
        $fraction = isset($parts[1]) ? rtrim($parts[1], '0') : '';

        return $fraction === '' ? $whole : $whole.'.'.$fraction;
    }
}
