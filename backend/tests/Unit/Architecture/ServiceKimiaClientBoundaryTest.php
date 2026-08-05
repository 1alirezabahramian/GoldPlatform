<?php

namespace Tests\Unit\Architecture;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class ServiceKimiaClientBoundaryTest extends TestCase
{
    public function test_application_services_do_not_call_kimia_client_or_http_directly(): void
    {
        $roots = [
            app_path('Application'),
        ];

        $forbidden = [
            'KimiaClient',
            'Illuminate\\Support\\Facades\\Http',
            'Http::',
        ];

        $violations = $this->scan($roots, $forbidden);

        $this->assertSame(
            [],
            $violations,
            "Application services must delegate Kimia transport to approved repositories:\n".implode("\n", $violations)
        );
    }

    public function test_direct_kimia_client_commands_are_limited_to_known_manual_tools(): void
    {
        $commandsRoot = app_path('Console/Commands');
        $allowed = [
            'SyncKimiaCoins.php',
            'SyncKimiaCurrencies.php',
            'TestKimiaConnection.php',
        ];

        $actual = [];

        if (is_dir($commandsRoot)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($commandsRoot, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());

                if (str_contains($contents, 'KimiaClient')) {
                    $actual[] = $file->getFilename();
                }
            }
        }

        sort($actual);
        sort($allowed);

        $this->assertSame(
            $allowed,
            $actual,
            'Direct KimiaClient command access is a controlled legacy/manual exception. No new command may bypass repositories.'
        );
    }

    /**
     * @param array<int, string> $roots
     * @param array<int, string> $forbidden
     * @return array<int, string>
     */
    private function scan(array $roots, array $forbidden): array
    {
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

        return $violations;
    }
}
