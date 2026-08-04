<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Contracts\TenantScopedBalanceProjectionRepository;
use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Infrastructure\Financial\InMemory\InMemoryTenantScopedIdempotencyRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class TenantScopedRepositoryContractsTest extends TestCase
{
    #[Test]
    public function every_tenant_scoped_repository_method_requires_financial_scope_first(): void
    {
        foreach ([
            TenantScopedJournalRepository::class,
            TenantScopedFinancialEventStore::class,
            TenantScopedIdempotencyRegistry::class,
            TenantScopedBalanceProjectionRepository::class,
        ] as $contract) {
            $reflection = new ReflectionClass($contract);

            foreach ($reflection->getMethods() as $method) {
                $parameters = $method->getParameters();

                self::assertNotEmpty($parameters, $contract.'::'.$method->getName().' must require FinancialScope.');
                self::assertSame(
                    FinancialScope::class,
                    $parameters[0]->getType()?->getName(),
                    $contract.'::'.$method->getName().' must receive FinancialScope as its first argument.',
                );
            }
        }
    }

    #[Test]
    public function the_same_idempotency_key_is_isolated_between_tenants(): void
    {
        $registry = new InMemoryTenantScopedIdempotencyRegistry();
        $tenantA = new FinancialScope('tenant-a');
        $tenantB = new FinancialScope('tenant-b');
        $key = IdempotencyKey::fromString('order:123:submit');
        $record = new IdempotencyRecord(
            key: $key,
            operation: 'order.submit',
            requestHash: hash('sha256', 'request-a'),
            traceId: TraceId::generate(),
        );

        $registry->claim($tenantA, $record);

        self::assertSame($record, $registry->find($tenantA, $key));
        self::assertNull($registry->find($tenantB, $key));
    }
}
