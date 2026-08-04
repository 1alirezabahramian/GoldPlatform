<?php

namespace App\Integrations\Kimia\Services;

use App\Integrations\Kimia\Client\KimiaClient;
use App\Integrations\Kimia\Exceptions\KimiaException;
use Throwable;

final class KimiaReadValidatorService
{
    public function __construct(private readonly KimiaClient $client)
    {
    }

    /**
     * @return array{ok: bool, checked_at: string, endpoints: array<string, array<string, mixed>>}
     */
    public function validate(?int $accountId = null): array
    {
        $checks = [
            'accounts' => fn () => $this->client->get('/api/account', ['Type' => 3])->json(),
            'coins' => fn () => $this->client->get('/api/product/coins')->json(),
            'currencies' => fn () => $this->client->get('/api/product/currencies')->json(),
            'barcodes' => fn () => $this->client->get('/api/barcode')->json(),
        ];

        if ($accountId !== null && $accountId > 0) {
            $checks['balance'] = fn () => $this->client
                ->get("/api/voucher/balance/{$accountId}", ['includePeaks' => 'false'])
                ->json();
            $checks['transactions'] = fn () => $this->client
                ->get("/api/voucher/transactions/{$accountId}", [
                    'pageNumber' => 0,
                    'pageSize' => 20,
                    'descending' => 'true',
                ])
                ->json();
        }

        $results = [];

        foreach ($checks as $name => $check) {
            $started = hrtime(true);

            try {
                $payload = $check();
                $results[$name] = [
                    'status' => 'pass',
                    'shape' => $this->shape($payload),
                    'items' => $this->countRows($payload),
                    'duration_ms' => round((hrtime(true) - $started) / 1_000_000, 2),
                ];
            } catch (Throwable $exception) {
                $results[$name] = [
                    'status' => 'fail',
                    'error_type' => $this->errorType($exception),
                    'duration_ms' => round((hrtime(true) - $started) / 1_000_000, 2),
                ];
            }
        }

        return [
            'ok' => collect($results)->every(fn (array $result): bool => $result['status'] === 'pass'),
            'checked_at' => now()->toIso8601String(),
            'endpoints' => $results,
        ];
    }

    private function shape(mixed $payload): string
    {
        if (! is_array($payload)) {
            return get_debug_type($payload);
        }

        if (array_is_list($payload)) {
            return 'list';
        }

        foreach (['data', 'items', 'result', 'Items'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return "wrapped:{$key}";
            }
        }

        return 'object';
    }

    private function countRows(mixed $payload): int
    {
        if (! is_array($payload)) {
            return 0;
        }

        if (array_is_list($payload)) {
            return count($payload);
        }

        foreach (['data', 'items', 'result', 'Items'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return count($payload[$key]);
            }
        }

        return count($payload);
    }

    private function errorType(Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        return match (true) {
            str_contains($message, '401'), str_contains($message, 'unauthorized') => 'unauthorized',
            str_contains($message, '403'), str_contains($message, 'forbidden') => 'forbidden',
            str_contains($message, 'timeout'), str_contains($message, 'timed out') => 'timeout',
            str_contains($message, 'connection') => 'network',
            str_contains($message, 'json') => 'invalid_json',
            $exception instanceof KimiaException => 'kimia_error',
            default => 'unknown',
        };
    }
}
