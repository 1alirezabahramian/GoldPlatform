<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Psr4ComplianceTest extends TestCase
{
    #[Test]
    public function app_types_match_their_psr4_paths(): void
    {
        $appPath = dirname(__DIR__, 3).'/app';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appPath));

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }
            $contents = file_get_contents($file->getPathname());
            preg_match('/namespace\s+([^;]+);/', $contents, $namespaceMatch);
            preg_match('/(?:class|interface|trait|enum)\s+(\w+)/', $contents, $typeMatch);
            if (! isset($namespaceMatch[1], $typeMatch[1])) {
                continue;
            }
            $relativePath = str_replace([$appPath.'/', '/', '.php'], ['', '\\', ''], $file->getPathname());
            $this->assertSame(
                'App\\'.$relativePath,
                $namespaceMatch[1].'\\'.$typeMatch[1],
                'PSR-4 mismatch in '.$file->getPathname()
            );
        }
    }

    #[Test]
    public function application_does_not_reference_legacy_kimia_paths(): void
    {
        $appPath = dirname(__DIR__, 3).'/app';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appPath));
        $forbidden = [
            'App\\Services\\KimiaService',
            'App\\Repositories\\Kimia\\AccountRepository',
            'App\\Repositories\\Kimia\\VoucherRepository',
        ];
        $violations = [];

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }
            $contents = (string) file_get_contents($file->getPathname());
            foreach ($forbidden as $legacyPath) {
                if (str_contains($contents, $legacyPath)) {
                    $violations[] = $file->getPathname().' => '.$legacyPath;
                }
            }
        }

        $this->assertSame([], $violations, "Legacy Kimia dependencies:\n".implode("\n", $violations));
    }
}
