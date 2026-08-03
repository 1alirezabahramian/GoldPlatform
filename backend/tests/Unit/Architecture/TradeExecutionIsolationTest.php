<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TradeExecutionIsolationTest extends TestCase
{
    #[Test]
    public function incomplete_trade_execution_service_is_not_referenced_by_routes_or_controllers(): void
    {
        $paths = [
            base_path('routes'),
            app_path('Http/Controllers'),
        ];

        $violations = [];

        foreach ($paths as $path) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());

                if (str_contains($contents, 'TradeService')) {
                    $violations[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertSame(
            [],
            $violations,
            'TradeService is intentionally blocked until order fields, ledger account resolution, idempotency, and audit rules are confirmed.'
        );
    }
}
