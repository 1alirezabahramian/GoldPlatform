<?php

namespace Tests\Unit\Architecture;

use Tests\TestCase;

class OutboxSensitiveReplayBoundaryTest extends TestCase
{
    public function test_sensitive_outbox_handlers_are_not_registered_without_approved_ground_truth(): void
    {
        $handlers = config('outbox.handlers', []);

        $this->assertSame([], $handlers);
    }

    public function test_admin_routes_do_not_expose_manual_settlement_retry_or_outbox_replay(): void
    {
        $routes = file_get_contents(base_path('routes/api.php'));

        $this->assertIsString($routes);
        $this->assertStringNotContainsString('/settlements/', $routes);
        $this->assertStringNotContainsString('/retry', $routes);
        $this->assertStringNotContainsString('/replay', $routes);
        $this->assertStringContainsString("Route::get('/outbox'", $routes);
    }
}
