<?php

namespace App\Console\Commands;

use App\Integrations\Kimia\Client\KimiaClient;
use App\Models\KimiaCurrency;
use Illuminate\Console\Command;
use Throwable;

class SyncKimiaCurrencies extends Command
{
    protected $signature = 'kimia:sync-currencies';

    protected $description = 'Synchronize currencies from Kimia';

    public function handle(KimiaClient $kimia): int
    {
        $this->info('Connecting to Kimia...');

        try {
            $currencies = $kimia->get('/api/product/currencies')->json();
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Kimia connection failed: '.$exception->getMessage());
            return self::FAILURE;
        }

        if (! is_array($currencies) || $currencies === []) {
            $this->error('No currencies received from Kimia.');
            return self::FAILURE;
        }

        $received = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($currencies as $currency) {
            if (! is_array($currency) || ! isset($currency['CurrencyId'])) {
                $skipped++;
                continue;
            }

            $received++;
            $model = KimiaCurrency::query()
                ->where('kimia_id', (int) $currency['CurrencyId'])
                ->first();
            $data = [
                'name' => $currency['Name'] ?? null,
                'is_visible' => $currency['IsVisible'] ?? null,
            ];

            if ($model === null) {
                KimiaCurrency::create([
                    'kimia_id' => (int) $currency['CurrencyId'],
                    ...$data,
                    'synced_at' => now(),
                ]);
                $created++;
                continue;
            }

            $model->fill($data);
            if (! $model->isDirty()) {
                $skipped++;
                continue;
            }

            $model->synced_at = now();
            $model->save();
            $updated++;
        }

        $this->newLine();
        $this->table(['Received', 'Created', 'Updated', 'Skipped'], [[
            $received, $created, $updated, $skipped,
        ]]);
        $this->info('Kimia currency synchronization finished.');

        return self::SUCCESS;
    }
}
