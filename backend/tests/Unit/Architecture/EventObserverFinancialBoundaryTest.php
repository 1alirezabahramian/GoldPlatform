<?php

namespace Tests\Unit\Architecture;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class EventObserverFinancialBoundaryTest extends TestCase
{
    public function test_events_listeners_and_observers_do_not_execute_sensitive_financial_infrastructure(): void
    {
        $roots = [
            app_path('Events'),
            app_path('Listeners'),
            app_path('Observers'),
        ];

        $forbidden = [
            'KimiaClient',
            'Integrations\\Kimia\\Repositories',
            'SettlementService',
            'LedgerService',
            'WalletService',
            'OutboxDispatcher',
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
            "Events, listeners and observers must not hide Kimia or financial execution:\n".implode("\n", $violations)
        );
    }

    public function test_after_commit_hooks_do_not_hide_sensitive_execution(): void
    {
        $root = app_path();
        $hookNeedles = [
            'ShouldQueueAfterCommit',
            'afterCommit(',
            'after_commit',
            '$dispatchesEvents',
        ];
        $sensitiveNeedles = [
            'KimiaClient',
            'SettlementService',
            'LedgerService',
            'WalletService',
            'OutboxDispatcher',
        ];

        $violations = [];
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            $hasHook = collect($hookNeedles)->contains(fn (string $needle): bool => str_contains($contents, $needle));
            $hasSensitiveDependency = collect($sensitiveNeedles)->contains(fn (string $needle): bool => str_contains($contents, $needle));

            if ($hasHook && $hasSensitiveDependency) {
                $violations[] = $file->getPathname();
            }
        }

        $this->assertSame(
            [],
            $violations,
            "After-commit or model event hooks must not conceal sensitive financial execution:\n".implode("\n", $violations)
        );
    }
}
