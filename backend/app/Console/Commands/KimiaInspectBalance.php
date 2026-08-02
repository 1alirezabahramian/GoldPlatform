<?php

namespace App\Console\Commands;

use App\Repositories\Kimia\VoucherRepository;
use Illuminate\Console\Command;
use Throwable;

class KimiaInspectBalance extends Command
{
    protected $signature = 'kimia:inspect-balance
                            {accountId : Kimia account identifier}
                            {--include-peaks : Include Kimia gold and money peaks}
                            {--show-account-name : Display the Kimia account name}';

    protected $description = 'Read a Kimia account balance without changing any voucher';

    public function handle(VoucherRepository $repository): int
    {
        $accountId = filter_var(
            $this->argument('accountId'),
            FILTER_VALIDATE_INT
        );

        if ($accountId === false || $accountId <= 0) {
            $this->error('Account ID must be a positive integer.');

            return self::FAILURE;
        }

        $this->info('Reading Kimia balance (read-only)...');

        try {
            $balances = $repository->balance(
                $accountId,
                (bool) $this->option('include-peaks')
            );
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if (! array_is_list($balances) || $balances === []) {
            $this->warn('No balance rows were returned.');

            return self::SUCCESS;
        }

        $showAccountName = (bool) $this->option('show-account-name');
        $headers = ['AccountId'];

        if ($showAccountName) {
            $headers[] = 'AccountName';
        }

        $headers = [
            ...$headers,
            'GroupId',
            'Weight',
            'Money',
            'CurrencyId',
            'CurrencySymbol',
        ];

        $rows = [];

        foreach ($balances as $balance) {
            if (! is_array($balance)) {
                continue;
            }

            $row = [
                $balance['AccountId'] ?? null,
            ];

            if ($showAccountName) {
                $row[] = $balance['AccountName'] ?? null;
            }

            $rows[] = [
                ...$row,
                $balance['GroupId'] ?? null,
                $balance['Weight'] ?? null,
                $balance['Money'] ?? null,
                $balance['CurrencyId'] ?? null,
                $balance['CurrencySymbol'] ?? null,
            ];
        }

        if ($rows === []) {
            $this->warn('Kimia returned no readable balance rows.');

            return self::SUCCESS;
        }

        $this->table(
            $headers,
            $rows
        );

        $this->line(
            'Raw Kimia values are shown without sign or unit conversion.'
        );

        if (! $showAccountName) {
            $this->line('Account names are omitted by default for safe report sharing.');
        }

        return self::SUCCESS;
    }
}
