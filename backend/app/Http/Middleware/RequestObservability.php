<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RequestObservability
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('observability.request_logging_enabled', true)) {
            return $next($request);
        }

        $startedAt = hrtime(true);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $this->logRequest($request, 500, $startedAt, true);

            throw $exception;
        }

        $status = $response->getStatusCode();
        $durationMs = $this->durationMs($startedAt);
        $slowThresholdMs = max(1, (int) config('observability.slow_request_threshold_ms', 1000));
        $isSlow = $durationMs >= $slowThresholdMs;
        $isServerError = $status >= 500;
        $shouldLogSuccess = (bool) config('observability.log_successful_requests', false);

        if ($isServerError || $isSlow || $shouldLogSuccess) {
            $this->writeLog($request, $status, $durationMs, $isSlow);
        }

        $response->headers->set('Server-Timing', sprintf('app;dur=%.2f', $durationMs));

        return $response;
    }

    private function logRequest(Request $request, int $status, int $startedAt, bool $exception): void
    {
        $durationMs = $this->durationMs($startedAt);
        $slowThresholdMs = max(1, (int) config('observability.slow_request_threshold_ms', 1000));

        $this->writeLog(
            $request,
            $status,
            $durationMs,
            $durationMs >= $slowThresholdMs,
            $exception,
        );
    }

    private function writeLog(
        Request $request,
        int $status,
        float $durationMs,
        bool $slow,
        bool $exception = false,
    ): void {
        $context = [
            'event' => 'http_request_observed',
            'request_id' => $request->attributes->get('request_id'),
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $status,
            'duration_ms' => round($durationMs, 2),
            'slow' => $slow,
            'exception' => $exception,
        ];

        if ($exception || $status >= 500) {
            Log::error('HTTP request failed.', $context);

            return;
        }

        if ($slow) {
            Log::warning('Slow HTTP request detected.', $context);

            return;
        }

        Log::info('HTTP request completed.', $context);
    }

    private function durationMs(int $startedAt): float
    {
        return (hrtime(true) - $startedAt) / 1_000_000;
    }
}
