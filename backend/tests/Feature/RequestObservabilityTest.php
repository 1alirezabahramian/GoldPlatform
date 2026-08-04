<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RequestObservabilityTest extends TestCase
{
    public function test_health_response_contains_server_timing_header(): void
    {
        config()->set('observability.request_logging_enabled', true);

        $response = $this->get('/up');

        $response->assertOk();
        $response->assertHeader('Server-Timing');
    }

    public function test_successful_request_is_logged_when_enabled(): void
    {
        config()->set('observability.request_logging_enabled', true);
        config()->set('observability.log_successful_requests', true);
        config()->set('observability.slow_request_threshold_ms', 60_000);

        Log::spy();

        $this->get('/up')->assertOk();

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'HTTP request completed.'
                    && $context['event'] === 'http_request_observed'
                    && $context['method'] === 'GET'
                    && $context['path'] === 'up'
                    && $context['status'] === 200
                    && is_numeric($context['duration_ms'])
                    && $context['slow'] === false
                    && $context['exception'] === false;
            });
    }

    public function test_observability_can_be_disabled(): void
    {
        config()->set('observability.request_logging_enabled', false);

        Log::spy();

        $response = $this->get('/up');

        $response->assertOk();
        $response->assertHeaderMissing('Server-Timing');
        Log::shouldNotHaveReceived('info');
        Log::shouldNotHaveReceived('warning');
        Log::shouldNotHaveReceived('error');
    }
}
