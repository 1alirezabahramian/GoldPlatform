<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinancialDeletionBoundaryTest extends TestCase
{
    #[Test]
    public function application_layers_do_not_directly_delete_financial_records(): void
    {
        $roots = [
            app_path('Http/Controllers'),
            app_path('Services'),
            app_path('Console/Commands'),
            app_path('Jobs'),
        ];

        $forbiddenPatterns = [
            '/FinancialTransaction(?:::|->).*?(?:forceDelete|delete)\s*\(/s',
            '/LedgerEntry(?:::|->).*?(?:forceDelete|delete)\s*\(/s',
            '/WalletAccount(?:::|->).*?(?:forceDelete|delete)\s*\(/s',
            '/->ledgerEntries\(\).*?(?:forceDelete|delete)\s*\(/s',
        ];

        $violations = [];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());

                foreach ($forbiddenPatterns as $pattern) {
                    if (preg_match($pattern, $contents) === 1) {
                        $violations[] = str_replace(base_path().'/', '', $file->getPathname());
                        break;
                    }
                }
            }
        }

        $this->assertSame(
            [],
            array_values(array_unique($violations)),
            'Financial records must be archived or reversed, never directly deleted.'
        );
    }
}
