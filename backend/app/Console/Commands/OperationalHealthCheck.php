<?php

namespace App\Console\Commands;

use App\Support\OperationalHealthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class OperationalHealthCheck extends Command
{
    protected $signature = 'ops:health {--json} {--fail-on-degraded}';

    protected $description = 'Check database, Redis, storage, queue, outbox and Kimia safety state.';

    public function handle(OperationalHealthService $health): int
    {
        $snapshot = $health->snapshot();

        Log::info('Operational health check completed.', [
            'status' => $snapshot['status'],
            'components' => collect($snapshot['components'])
                ->map(fn (array $component): string => (string) $component['status'])
                ->all(),
        ]);

        if ($this->option('json')) {
            $this->line((string) json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } else {
            $this->info('Operational health: '.$snapshot['status']);
            foreach ($snapshot['components'] as $name => $component) {
                $this->line(sprintf('%s: %s', $name, $component['status']));
            }
        }

        if ($this->option('fail-on-degraded') && $snapshot['status'] !== 'ok') {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
