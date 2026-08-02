<?php

namespace App\Console\Commands;

use App\Repositories\Kimia\VoucherRepository;
use Illuminate\Console\Command;
use Throwable;

class KimiaInspectTransactions extends Command
{
    protected $signature = 'kimia:inspect-transactions
                            {accountId : Kimia account identifier}
                            {--page=0 : Zero-based page number}
                            {--size=50 : Number of records per page}';

    protected $description = 'Read Kimia transactions without creating or changing vouchers';

    public function handle(VoucherRepository $repository): int
    {
        $accountId = filter_var($this->argument('accountId'), FILTER_VALIDATE_INT);
        $pageNumber = filter_var($this->option('page'), FILTER_VALIDATE_INT);
        $pageSize = filter_var($this->option('size'), FILTER_VALIDATE_INT);

        if ($accountId === false || $accountId <= 0) {
            $this->error('Account ID must be a positive integer.');

            return self::FAILURE;
        }

        if ($pageNumber === false || $pageNumber < 0) {
            $this->error('Page number must be zero or greater.');

            return self::FAILURE;
        }

        if ($pageSize === false || $pageSize <= 0) {
            $this->error('Page size must be a positive integer.');

            return self::FAILURE;
        }

        $this->info('Reading Kimia transactions (read-only)...');

        try {
            $result = $repository->transactions(
                $accountId,
                $pageNumber,
                $pageSize
            );
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $items = $result['Items'] ?? [];

        if (! is_array($items) || $items === []) {
            $this->warn('No transaction records were returned.');

            return self::SUCCESS;
        }

        $rows = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $rows[] = [
                $item['RecordId'] ?? null,
                $item['Action'] ?? null,
                $item['ActionName'] ?? null,
                $item['ProductId'] ?? null,
                $item['ProductName'] ?? null,
                $item['Weight'] ?? null,
                $item['Quantity'] ?? null,
                $item['SumMoney'] ?? null,
            ];
        }

        $this->table(
            [
                'RecordId',
                'Action',
                'ActionName',
                'ProductId',
                'ProductName',
                'Weight',
                'Quantity',
                'SumMoney',
            ],
            $rows
        );

        $this->line(sprintf(
            'Page %s of %s; total records: %s',
            $result['PageNumber'] ?? $pageNumber,
            $result['TotalPages'] ?? '?',
            $result['TotalCount'] ?? '?'
        ));

        return self::SUCCESS;
    }
}
