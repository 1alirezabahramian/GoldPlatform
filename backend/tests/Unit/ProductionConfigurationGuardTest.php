<?php

namespace Tests\Unit;

use App\Support\ProductionConfigurationGuard;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductionConfigurationGuardTest extends TestCase
{
    #[Test]
    public function safe_production_configuration_passes(): void
    {
        config()->set('app.env', 'production');
        config()->set('app.debug', false);
        config()->set('app.url', 'https://goldplatform.test');
        config()->set('database.default', 'mysql');
        config()->set('session.secure', true);
        config()->set('services.kimia.read_only', true);
        config()->set('kimia_write.enabled', false);

        $this->assertSame([], app(ProductionConfigurationGuard::class)->violations());
    }

    #[Test]
    public function unsafe_values_are_reported(): void
    {
        config()->set('app.env', 'local');
        config()->set('app.debug', true);
        config()->set('app.url', 'http://localhost');
        config()->set('database.default', 'sqlite');
        config()->set('session.secure', false);
        config()->set('services.kimia.read_only', false);
        config()->set('kimia_write.enabled', true);

        $violations = app(ProductionConfigurationGuard::class)->violations();

        $this->assertCount(7, $violations);
    }
}
