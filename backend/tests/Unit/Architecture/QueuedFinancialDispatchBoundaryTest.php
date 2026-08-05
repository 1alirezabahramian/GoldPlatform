<?php

namespace Tests\Unit\Architecture;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\TestCase;

class QueuedFinancialDispatchBoundaryTest extends TestCase
{
    public function test_sensitive_financial_or_kimia_work_is_not_hidden_in_queued_classes(): void
    {
        $appPath = app_path();
        $sensitiveTerms = [
            'Kimia',
            'Settlement',
            'Voucher',
            'Wallet',
            'Ledger',
            'Balance',
            'Outbox',
        ];

        $violations = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($appPath)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $content = file_get_contents($file->getPathname());

            if (! is_string($content) || ! str_contains($content, 'ShouldQueue')) {
                continue;
            }

            foreach ($sensitiveTerms as $term) {
                if (str_contains($content, $term)) {
                    $violations[] = $file->getPathname().':'.$term;
                }
            }
        }

        $this->assertSame([], $violations);
    }

    public function test_routes_do_not_dispatch_sensitive_jobs_or_queue_commands(): void
    {
        $routeFiles = [
            base_path('routes/api.php'),
            base_path('routes/web.php'),
            base_path('routes/console.php'),
        ];

        foreach ($routeFiles as $routeFile) {
            if (! is_file($routeFile)) {
                continue;
            }

            $content = file_get_contents($routeFile);

            $this->assertIsString($content);
            $this->assertStringNotContainsString('::dispatch(', $content);
            $this->assertStringNotContainsString('Bus::dispatch(', $content);
            $this->assertStringNotContainsString('Queue::push(', $content);
            $this->assertStringNotContainsString('Artisan::queue(', $content);
        }
    }
}
