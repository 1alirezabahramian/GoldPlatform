<?php

namespace Tests\Unit\Architecture;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class ServiceKimiaClientBoundaryTest extends TestCase
{
    public function test_application_services_and_commands_do_not_call_kimia_client_or_http_directly(): void
    {
        $roots = [
            app_path('Application'),
            app_path('Services'),
            app_path('Console/Commands'),
        ];

        $forbidden = [
            'KimiaClient',
            'Illuminate\\Support\\Facades\\Http',
            'Http::',
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

                foreach ($forbidden as $needle) {
                    if (str_contains($contents, $needle)) {
                        $violations[] = $file->getPathname().' contains '.$needle;
                    }
                }
            }
        }

        $this->assertSame(
            [],
            $violations,
            "Application services and commands must delegate Kimia transport to the integration repositories/client layer:\n".implode("\n", $violations)
        );
    }
}
