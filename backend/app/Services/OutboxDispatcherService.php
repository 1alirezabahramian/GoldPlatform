<?php

namespace App\Services;

use App\Contracts\OutboxEventHandler;
use App\Models\OutboxMessage;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class OutboxDispatcherService
{
    /** @return array{processed:int,failed:int,skipped:int} */
    public function dispatchPending(?int $limit = null): array
    {
        $limit ??= (int) config('outbox.batch_size', 50);
        $result = ['processed' => 0, 'failed' => 0, 'skipped' => 0];

        $ids = OutboxMessage::query()
            ->whereNull('processed_at')
            ->where(fn ($query) => $query->whereNull('available_at')->orWhere('available_at', '<=', now()))
            ->where('attempts', '<', (int) config('outbox.max_attempts', 5))
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        foreach ($ids as $id) {
            $status = $this->dispatchOne((int) $id);
            $result[$status]++;
        }

        return $result;
    }

    private function dispatchOne(int $id): string
    {
        return DB::transaction(function () use ($id): string {
            $message = OutboxMessage::query()->lockForUpdate()->find($id);

            if (! $message || $message->processed_at !== null) {
                return 'skipped';
            }

            $handlerClass = config('outbox.handlers.'.$message->event_type);

            if (! is_string($handlerClass) || $handlerClass === '') {
                $this->recordFailure($message, new RuntimeException('No approved outbox handler registered for event type.'));

                return 'failed';
            }

            $handler = app($handlerClass);
            if (! $handler instanceof OutboxEventHandler) {
                $this->recordFailure($message, new RuntimeException('Configured outbox handler does not implement the required contract.'));

                return 'failed';
            }

            try {
                $handler->handle($message);
                $message->forceFill([
                    'processed_at' => now(),
                    'last_error' => null,
                ])->save();

                return 'processed';
            } catch (Throwable $exception) {
                $this->recordFailure($message, $exception);

                return 'failed';
            }
        }, 3);
    }

    private function recordFailure(OutboxMessage $message, Throwable $exception): void
    {
        $attempts = $message->attempts + 1;
        $delay = (int) config('outbox.retry_delay_seconds', 60);

        $message->forceFill([
            'attempts' => $attempts,
            'available_at' => $attempts >= (int) config('outbox.max_attempts', 5)
                ? null
                : now()->addSeconds($delay * $attempts),
            'last_error' => $exception::class,
        ])->save();
    }
}
