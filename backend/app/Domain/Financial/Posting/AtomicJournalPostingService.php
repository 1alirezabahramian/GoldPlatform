<?php

namespace App\Domain\Financial\Posting;

use App\Domain\Financial\Contracts\IdempotencyRegistry;
use App\Domain\Financial\Contracts\JournalProjectionApplier;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Financial\Persistence\FinancialEventStore;
use App\Domain\Financial\Persistence\JournalRepository;
use App\Domain\Financial\ValueObjects\TraceId;
use DateTimeImmutable;
use DomainException;

/**
 * @deprecated Use TenantScopedAtomicJournalPostingService.
 * New financial posting flows must require FinancialScope.
 */
final readonly class AtomicJournalPostingService
{
    private const OPERATION = 'financial.journal.post';

    public function __construct(
        private IdempotencyRegistry $idempotency,
        private ConcurrencyGuard $concurrency,
        private AtomicFinancialOperation $atomic,
        private JournalRepository $journals,
        private FinancialEventStore $events,
        private JournalProjectionApplier $projection,
    ) {}

    public function post(JournalDocument $draft, string $requestHash): JournalDocument
    {
        $journal = $draft->journal();
        $key = $journal->idempotencyKey();

        return $this->concurrency->synchronized(
            'financial:idempotency:'.$key->value(),
            function () use ($draft, $journal, $key, $requestHash): JournalDocument {
                $existing = $this->idempotency->find($key);

                if ($existing !== null) {
                    if (! $existing->matches(self::OPERATION, $requestHash)) {
                        throw new DomainException('Idempotency key was already used for a different request.');
                    }

                    $reference = $existing->resultReference();
                    if ($reference === null) {
                        throw new DomainException('Idempotent operation exists without a result reference.');
                    }

                    $saved = $this->journals->findByTraceId(TraceId::fromString($reference));
                    if ($saved === null) {
                        throw new DomainException('Idempotent result reference cannot be resolved.');
                    }

                    return $saved;
                }

                return $this->atomic->execute(function () use ($draft, $journal, $key, $requestHash): JournalDocument {
                    $posted = $draft->post();
                    $this->journals->save($posted);
                    $this->projection->apply($posted);
                    $this->events->append(new FinancialEvent(
                        name: 'financial.journal.posted',
                        traceId: $journal->traceId(),
                        correlationId: $journal->correlationId(),
                        idempotencyKey: $key,
                        occurredAt: new DateTimeImmutable(),
                        payload: ['trace_id' => $journal->traceId()->value()],
                    ));
                    $this->idempotency->claim(new IdempotencyRecord(
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
