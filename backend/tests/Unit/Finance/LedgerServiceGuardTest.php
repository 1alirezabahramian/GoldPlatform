<?php

namespace Tests\Unit\Finance;

use App\Models\FinancialTransaction;
use App\Services\LedgerService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LedgerServiceGuardTest extends TestCase
{
    #[Test]
    public function it_rejects_an_unpersisted_financial_transaction(): void
    {
        $service = app(LedgerService::class);

        $this->expectException(InvalidArgumentException::class);

        $service->transfer(
            transaction: new FinancialTransaction(),
            fromAccountId: 1,
            toAccountId: 2,
            amount: '1.000000'
        );
    }

    #[Test]
    public function it_rejects_a_transfer_between_the_same_account(): void
    {
        $transaction = new FinancialTransaction();
        $transaction->exists = true;
        $transaction->setAttribute('id', 10);

        $service = app(LedgerService::class);

        $this->expectException(InvalidArgumentException::class);

        $service->transfer(
            transaction: $transaction,
            fromAccountId: 1,
            toAccountId: 1,
            amount: '1.000000'
        );
    }

    #[Test]
    #[DataProvider('invalidAmounts')]
    public function it_rejects_non_positive_or_invalid_amounts(string $amount): void
    {
        $transaction = new FinancialTransaction();
        $transaction->exists = true;
        $transaction->setAttribute('id', 10);

        $service = app(LedgerService::class);

        $this->expectException(InvalidArgumentException::class);

        $service->transfer(
            transaction: $transaction,
            fromAccountId: 1,
            toAccountId: 2,
            amount: $amount
        );
    }

    public static function invalidAmounts(): array
    {
        return [
            'zero' => ['0'],
            'negative' => ['-0.000001'],
            'not numeric' => ['invalid'],
        ];
    }

    #[Test]
    public function it_rejects_an_unsupported_entry_type_before_database_access(): void
    {
        $transaction = new FinancialTransaction();
        $transaction->exists = true;
        $transaction->setAttribute('id', 10);

        $service = app(LedgerService::class);

        $this->expectException(InvalidArgumentException::class);

        $service->createEntry(
            transaction: $transaction,
            walletAccountId: 1,
            entryType: 'increase',
            amount: '1.000000'
        );
    }
}
