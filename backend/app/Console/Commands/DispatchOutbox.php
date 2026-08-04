<?php

namespace App\Console\Commands;

use App\Services\OutboxDispatcherService;
use Illuminate\Console\Command;

final class DispatchOutbox extends Command
{
    protected $signature = 'outbox:dispatch {--limit=} {--fail-on-error}';

    protected $description = 'Dispatch approved pending outbox messages with locking and retry controls.';

    public function handle(OutboxDispatcherService $dispatcher): int
    {
        $limit = $this->option('limit');
        $result = $dispatcher->dispatchPending($limit === null ? null : (int) $limit);

        $this->line((string) json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

        return $this->option('fail-on-error') && $result['failed'] > 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
