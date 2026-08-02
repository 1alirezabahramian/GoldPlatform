<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KimiaSafetyStatusCommandTest extends TestCase
{
    #[Test]
    public function it_succeeds_only_when_live_writes_are_disabled(): void
    {
        config()->set('services.kimia.writes_enabled', false);

        $this->artisan('kimia:safety-status')
            ->expectsOutputToContain('disabled')
            ->assertSuccessful();

        config()->set('services.kimia.writes_enabled', true);

        $this->artisan('kimia:safety-status')
            ->expectsOutputToContain('ENABLED')
            ->assertFailed();
    }
}
