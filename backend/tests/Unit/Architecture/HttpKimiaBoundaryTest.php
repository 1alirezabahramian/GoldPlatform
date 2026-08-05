<?php

namespace Tests\Unit\Architecture;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\TestCase;

class HttpKimiaBoundaryTest extends TestCase
{
    public function test_controllers_and_routes_do_not_call_kimia_infrastructure_directly(): void
    {
        $files = $this->phpFiles([
            app_path('Http/Controllers'),
            base_path('routes'),
        ]);

        $forbidden = [
            'KimiaClient',
            'Integrations\\Kimia\\Client',
            'Integrations\\Kimia\\Repositories',
            "Http::get(",
            "Http::post(",
            "Http::put(",
            "Http::patch(",
            "Http::delete(",
        ];

        $violations = [];

        foreach ($files as $file) {
            $contents = file_get_contents($file);

            $this->assertIsString($contents);

            foreach ($forbidden as $needle) {
                if (str_contains($contents, $needle)) {
                    $violations[] = sprintf('%s contains %s', $file, $needle);
                }
            }
        }

        $this->assertSame(
            [],
            $violations,
            "Controllers and routes must use application services, not Kimia or HTTP infrastructure directly:\n".implode("\n", $violations)
        );
    }

    /**
     * @param  array<int, string>  $roots
     * @return array<int, string>
     */
    private function phpFiles(array $roots): array
    {
        $files = [];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root)
            );

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $files[] = $file->getPathname();
                }
            }
        }

        sort($files);

        return $files;
    }
}
