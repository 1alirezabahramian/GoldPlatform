<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinancialExecutionBoundaryTest extends TestCase
{
    #[Test]
    public function incomplete_trade_service_is_not_referenced_by_routes_or_controllers(): void
    {
        $paths = [
            base_path('routes'),
            app_path('Http/Controllers'),
        ];

        foreach ($paths as $path) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());

                $this->assertStringNotContainsString(
                    'TradeService',
                    $contents,
                    "Incomplete TradeService must not be reachable from HTTP layer: {$file->getPathname()}"
                );
            }
        }
    }
}
