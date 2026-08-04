<?php

namespace App\Console\Commands;

use App\Integrations\Kimia\Client\KimiaClient;
use App\Models\KimiaCoin;
use Illuminate\Console\Command;
use Throwable;

class SyncKimiaCoins extends Command
{
    protected $signature = 'kimia:sync-coins';

    protected $description = 'Synchronize coins from Kimia';

    public function handle(KimiaClient $kimia): int
    {
        $this->info('Connecting to Kimia...');

        try {
            $coins = $kimia->get('/api/product/coins');
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Kimia connection failed: '.$exception->getMessage());
            return self::FAILURE;
        }

        if (empty($coins)) {
            $this->error('No coins received from Kimia.');
            return self::FAILURE;
        }

        $received = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($coins as $coin) {
            if (! is_array($coin) || ! isset($coin['CoinId'], $coin['Type'])) {
                $skipped++;
                continue;
            }

            $received++;
            $model = KimiaCoin::query()->where('kimia_id', (int) $coin['CoinId'])->first();
            $data = [
                'name' => $coin['Name'] ?? null,
                'fineness' => $coin['Fineness'] ?? null,
                'weight' => $coin['Weight'] ?? null,
                'type' => (int) $coin['Type'],
                'is_visible' => $coin['IsVisible'] ?? null,
            ];

            if ($model === null) {
                KimiaCoin::create([
                    'kimia_id' => (int) $coin['CoinId'],
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
        $this->info('Kimia coin synchronization finished.');

        return self::SUCCESS;
    }
}
