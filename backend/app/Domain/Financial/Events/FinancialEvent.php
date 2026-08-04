<?php

namespace App\Domain\Financial\Events;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class FinancialEvent
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        private string $name,
        private TraceId $traceId,
        private CorrelationId $correlationId,
        private IdempotencyKey $idempotencyKey,
        private DateTimeImmutable $occurredAt,
        private array $payload = [],
    ) {
        if (trim($name) === '') {
            throw new InvalidArgumentException('Financial event name cannot be empty.');
        }
    }

    public function name(): string { return $this->name; }
    public function traceId(): TraceId { return $this->traceId; }
    public function correlationId(): CorrelationId { return $this->correlationId; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function occurredAt(): DateTimeImmutable { return $this->occurredAt; }

    /** @return array<string, mixed> */
    public function payload(): array { return $this->payload; }
}
