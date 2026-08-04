<?php

namespace Tests\Feature;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Contracts\TenantScopedJournalProjectionApplier;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\Journal\Journal;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Journal\JournalLine;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Financial\Posting\TenantScopedAtomicJournalPostingService;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

final class TenantScopedAtomicPostingDatabaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_post_is_persisted_once_and_replayed_without_duplicate_effects(): void
    {
        $projectionCalls = 0;
        $service = $this->service(new class($projectionCalls) implements TenantScopedJournalProjectionApplier {
            public function __construct(private int &$calls) {}

            public function apply(FinancialScope $scope, JournalDocument $document): void
            {
                $this->calls++;
            }
        });

        $scope = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $draft = $this->draft('request-1');

        $first = $service->post($scope, $draft, 'hash-1');
        $replayed = $service->post($scope, $draft, 'hash-1');

        self::assertSame('posted', $first->status()->value);
        self::assertSame($first->journal()->traceId()->value(), $replayed->journal()->traceId()->value());
        self::assertSame(1, $projectionCalls);
        self::assertSame(1, DB::table('financial_journals')->count());
        self::assertSame(2, DB::table('financial_journal_lines')->count());
        self::assertSame(1, DB::table('financial_events')->count());
        self::assertSame(1, DB::table('financial_idempotency_records')->count());
    }

    public function test_failure_inside_projection_rolls_back_journal_event_and_idempotency(): void
    {
        $service = $this->service(new class implements TenantScopedJournalProjectionApplier {
            public function apply(FinancialScope $scope, JournalDocument $document): void
            {
                throw new RuntimeException('Projection failed.');
            }
        });

        try {
            $service->post(
                new FinancialScope('tenant-a'),
                $this->draft('request-rollback'),
                'hash-rollback',
            );
            self::fail('Expected projection failure was not thrown.');
        } catch (RuntimeException $exception) {
            self::assertSame('Projection failed.', $exception->getMessage());
        }

        self::assertSame(0, DB::table('financial_journals')->count());
        self::assertSame(0, DB::table('financial_journal_lines')->count());
        self::assertSame(0, DB::table('financial_events')->count());
        self::assertSame(0, DB::table('financial_idempotency_records')->count());
    }

    public function test_same_idempotency_key_is_independent_between_tenants(): void
    {
        $service = $this->service(new class implements TenantScopedJournalProjectionApplier {
            public function apply(FinancialScope $scope, JournalDocument $document): void {}
        });

        $service->post(
            new FinancialScope('tenant-a'),
            $this->draft('shared-key'),
            'hash-a',
        );
        $service->post(
            new FinancialScope('tenant-b'),
            $this->draft('shared-key'),
            'hash-b',
        );

        self::assertSame(2, DB::table('financial_journals')->count());
        self::assertSame(2, DB::table('financial_events')->count());
        self::assertSame(2, DB::table('financial_idempotency_records')->count());
    }

    public function test_same_key_with_different_request_in_one_scope_is_rejected(): void
    {
        $service = $this->service(new class implements TenantScopedJournalProjectionApplier {
            public function apply(FinancialScope $scope, JournalDocument $document): void {}
        });

        $scope = new FinancialScope('tenant-a');
        $draft = $this->draft('conflicting-key');
        $service->post($scope, $draft, 'hash-one');

        $this->expectException(DomainException::class);
        $service->post($scope, $draft, 'hash-two');
    }

    private function service(TenantScopedJournalProjectionApplier $projection): TenantScopedAtomicJournalPostingService
    {
        $concurrency = new class implements ConcurrencyGuard {
            public function synchronized(string $resource, callable $operation): mixed
            {
                return $operation();
            }
        };

        return new TenantScopedAtomicJournalPostingService(
            $this->app->make(TenantScopedIdempotencyRegistry::class),
            $concurrency,
            $this->app->make(AtomicFinancialOperation::class),
            $this->app->make(TenantScopedJournalRepository::class),
            $this->app->make(TenantScopedFinancialEventStore::class),
            $projection,
        );
    }

    private function draft(string $idempotencyKey): JournalDocument
    {
        $asset = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');

        return JournalDocument::draft(new Journal(
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString($idempotencyKey),
            lines: [
                new JournalLine(
                    LedgerAccountId::fromString('customer:a:money'),
                    $asset,
                    JournalSide::DEBIT,
                    ExactDecimal::fromString('10'),
                ),
                new JournalLine(
                    LedgerAccountId::fromString('system:b:money'),
                    $asset,
                    JournalSide::CREDIT,
                    ExactDecimal::fromString('10'),
                ),
            ],
        ));
    }
}
