<?php

namespace App\Domain\Financial\Posting;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Contracts\TenantScopedJournalProjectionApplier;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\TraceId;
use DateTimeImmutable;
use DomainException;

final readonly class TenantScopedAtomicJournalPostingService
{
    private const OPERATION = 'financial.journal.post';

    public function __construct(
        private TenantScopedIdempotencyRegistry $idempotency,
        private ConcurrencyGuard $concurrency,
        private AtomicFinancialOperation $atomic,
        private TenantScopedJournalRepository $journals,
        private TenantScopedFinancialEventStore $events,
        private TenantScopedJournalProjectionApplier $projection,
    ) {}

    public function post(
        FinancialScope $scope,
        JournalDocument $draft,
        string $requestHash,
    ): JournalDocument {
        $journal = $draft->journal();
        $key = $journal->idempotencyKey();
        $lockKey = $scope->key().':idempotency:'.$key->value();

        return $this->concurrency->synchronized(
            $lockKey,
            function () use ($scope, $draft, $journal, $key, $requestHash): JournalDocument {
                $existing = $this->idempotency->find($scope, $key);

                if ($existing !== null) {
                    if (! $existing->matches(self::OPERATION, $requestHash)) {
                        throw new DomainException('Idempotency key was already used for a different request in this financial scope.');
                    }

                    $reference = $existing->resultReference();
                    if ($reference === null) {
                        throw new DomainException('Idempotent operation exists without a result reference.');
                    }

                    $saved = $this->journals->findByTraceId($scope, TraceId::fromString($reference));
                    if ($saved === null) {
                        throw new DomainException('Idempotent result reference cannot be resolved in this financial scope.');
                    }

                    return $saved;
                }

                return $this->atomic->execute(function () use ($scope, $draft, $journal, $key, $requestHash): JournalDocument {
                    $posted = $draft->post();
                    $this->journals->save($scope, $posted);
                    $this->projection->apply($scope, $posted);
                    $this->events->append($scope, new FinancialEvent(
                        name: 'financial.journal.posted',
                        traceId: $journal->traceId(),
                        correlationId: $journal->correlationId(),
                        idempotencyKey: $key,
                        occurredAt: new DateTimeImmutable(),
                        payload: [
                            'scope' => $scope->key(),
                            'trace_id' => $journal->traceId()->value(),
                        ],
                    ));
                    $this->idempotency->claim($scope, new IdempotencyRecord(
                        key: $key,
                        operation: self::OPERATION,
                        requestHash: $requestHash,
                        traceId: $journal->traceId(),
                        resultReference: $journal->traceId()->value(),
                    ));

                    return $posted;
                });
            },
        );
    }
}
