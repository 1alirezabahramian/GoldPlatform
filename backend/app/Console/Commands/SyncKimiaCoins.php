<?php

namespace App\Console\Commands;

use App\Models\KimiaCoin;
use App\Services\KimiaService;
use Illuminate\Console\Command;
use Throwable;

class SyncKimiaCoins extends Command
{
    protected $signature = 'kimia:sync-coins';

    protected $description = 'Synchronize coins from Kimia';

    public function handle(KimiaService $kimia): int
    {
        $this->info('Connecting to Kimia...');

        try {
            $coins = $kimia->get('/api/product/coins');
        } catch (Throwable $exception) {
            $this->error('Kimia connection failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (empty($coins)) {
            $this->error('No coins received from Kimia.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($coins as $coin) {
            if (! isset($coin['CoinId'], $coin['Type'])) {
                $skipped++;

                continue;
            }

            $model = KimiaCoin::updateOrCreate(
                [
                    'kimia_id' => $coin['CoinId'],
                ],
                [
                    'name' => $coin['Name'] ?? null,
                    'fineness' => $coin['Fineness'] ?? null,
                    'weight' => $coin['Weight'] ?? null,
                    'type' => $coin['Type'],
                    'is_visible' => $coin['IsVisible'] ?? null,
                    'synced_at' => now(),
                ]
            );

            if ($model->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->newLine();

        $this->table(
            ['Received', 'Created', 'Updated', 'Skipped'],
            [[count($coins), $created, $updated, $skipped]]
        );

        $this->info('Kimia coin synchronization finished.');

        return self::SUCCESS;
    }
}
