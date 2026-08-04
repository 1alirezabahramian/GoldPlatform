<?php

namespace Tests\Unit;

use App\Support\OperationalHealthService;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OperationalHealthServiceTest extends TestCase
{
    #[Test]
    public function healthy_dependencies_produce_an_ok_snapshot(): void
    {
        Redis::shouldReceive('connection')->once()->andReturn(new class
        {
            public function ping(): string
            {
                return 'PONG';
            }
        });

        config()->set('services.kimia.read_only', true);
        config()->set('kimia_write.enabled', false);

        $snapshot = app(OperationalHealthService::class)->snapshot();

        $this->assertSame('ok', $snapshot['components']['database']['status']);
        $this->assertSame('ok', $snapshot['components']['redis']['status']);
        $this->assertSame('ok', $snapshot['components']['kimia_safety']['status']);
        $this->assertArrayHasKey('checked_at', $snapshot);
    }

    #[Test]
    public function unsafe_kimia_write_state_degrades_health(): void
    {
        Redis::shouldReceive('connection')->once()->andReturn(new class
        {
            public function ping(): string
            {
                return 'PONG';
            }
        });

        config()->set('services.kimia.read_only', false);
        config()->set('kimia_write.enabled', true);

        $snapshot = app(OperationalHealthService::class)->snapshot();

        $this->assertSame('degraded', $snapshot['status']);
        $this->assertSame('degraded', $snapshot['components']['kimia_safety']['status']);
    }
}
