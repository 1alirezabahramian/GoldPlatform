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
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($appPath)
        );

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            preg_match('/namespace\s+([^;]+);/', $contents, $namespaceMatch);
            preg_match(
                '/(?:class|interface|trait|enum)\s+(\w+)/',
                $contents,
                $typeMatch
            );

            if (! isset($namespaceMatch[1], $typeMatch[1])) {
                continue;
            }

            $relativePath = str_replace(
                [$appPath.'/', '/', '.php'],
                ['', '\\', ''],
                $file->getPathname()
            );

            $this->assertSame(
                'App\\'.$relativePath,
                $namespaceMatch[1].'\\'.$typeMatch[1],
                'PSR-4 mismatch in '.$file->getPathname()
            );
        }
    }
}
