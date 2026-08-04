<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Contracts\BalanceProjectionRepository;
use App\Domain\Financial\Contracts\IdempotencyRegistry;
use App\Domain\Financial\Persistence\FinancialEventStore;
use App\Domain\Financial\Persistence\JournalRepository;
use App\Domain\Financial\Posting\AtomicJournalPostingService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class TenantScopedMigrationGuardTest extends TestCase
{
    #[Test]
    public function non_scoped_financial_contracts_are_explicitly_deprecated(): void
    {
        $legacyTypes = [
            BalanceProjectionRepository::class,
            IdempotencyRegistry::class,
            FinancialEventStore::class,
            JournalRepository::class,
            AtomicJournalPostingService::class,
        ];

        foreach ($legacyTypes as $legacyType) {
            $comment = (new ReflectionClass($legacyType))->getDocComment() ?: '';

            self::assertStringContainsString('@deprecated', $comment, $legacyType);
        }
    }
}
