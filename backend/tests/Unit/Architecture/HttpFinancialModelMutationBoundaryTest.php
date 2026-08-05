<?php

namespace Tests\Unit\Architecture;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class HttpFinancialModelMutationBoundaryTest extends TestCase
{
    public function test_controllers_and_routes_do_not_mutate_financial_models_directly(): void
    {
        $roots = [
            app_path('Http/Controllers'),
            base_path('routes'),
        ];

        $models = '(?:Wallet|WalletAccount|WalletTransaction|FinancialTransaction|LedgerEntry|BalanceReservation|Settlement)';
        $patterns = [
            '/'.$models.'::(?:create|forceCreate|updateOrCreate|firstOrCreate|insert|upsert|destroy)\s*\(/',
            '/'.$models.'::query\(\)[\s\S]{0,400}?->(?:update|delete|forceDelete)\s*\(/',
            '/new\s+'.$models.'\s*\(/',
        ];

        $violations = [];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $contents) === 1) {
                        $violations[] = $file->getPathname().' matches '.$pattern;
                    }
                }
            }
        }

        $this->assertSame(
            [],
            $violations,
            "HTTP layers must delegate financial mutations to approved application services:\n".implode("\n", $violations)
        );
    }
}
