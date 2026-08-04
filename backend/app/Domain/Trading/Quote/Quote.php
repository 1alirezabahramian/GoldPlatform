<?php

namespace App\Domain\Trading\Quote;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Enums\QuoteStatus;
use App\Domain\Trading\ValueObjects\QuoteId;
use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;

final readonly class Quote
{
    private function __construct(
        private QuoteId $id,
        private FinancialScope $scope,
        private TraceId $traceId,
        private CorrelationId $correlationId,
        private IdempotencyKey $idempotencyKey,
        private QuoteStatus $status,
        private DateTimeImmutable $requestedAt,
        private ?DateTimeImmutable $expiresAt = null,
    ) {
        if ($status === QuoteStatus::FROZEN && $expiresAt === null) {
            throw new InvalidArgumentException('A frozen quote requires an expiration time.');
        }
    }

    public static function request(
        FinancialScope $scope,
        TraceId $traceId,
        CorrelationId $correlationId,
        IdempotencyKey $idempotencyKey,
        DateTimeImmutable $requestedAt,
    ): self {
        return new self(
            QuoteId::generate(),
            $scope,
            $traceId,
            $correlationId,
            $idempotencyKey,
            QuoteStatus::REQUESTED,
            $requestedAt,
        );
    }

    public static function rehydrate(
        QuoteId $id,
        FinancialScope $scope,
        TraceId $traceId,
        CorrelationId $correlationId,
        IdempotencyKey $idempotencyKey,
        QuoteStatus $status,
        DateTimeImmutable $requestedAt,
        ?DateTimeImmutable $expiresAt,
    ): self {
        return new self(
            $id,
            $scope,
            $traceId,
            $correlationId,
            $idempotencyKey,
            $status,
            $requestedAt,
            $expiresAt,
        );
    }

    public function freeze(DateTimeImmutable $expiresAt): self
    {
        if ($this->status !== QuoteStatus::REQUESTED) {
            throw new DomainException('Only a requested quote can be frozen.');
        }

        if ($expiresAt <= $this->requestedAt) {
            throw new DomainException('Quote expiration must be after request time.');
        }

        return new self(
            $this->id,
            $this->scope,
            $this->traceId,
            $this->correlationId,
            $this->idempotencyKey,
            QuoteStatus::FROZEN,
            $this->requestedAt,
            $expiresAt,
        );
    }

    public function use(DateTimeImmutable $usedAt): self
    {
        if ($this->status !== QuoteStatus::FROZEN) {
            throw new DomainException('Only a frozen quote can be used.');
        }

        if ($this->expiresAt !== null && $usedAt >= $this->expiresAt) {
            throw new DomainException('Expired quote cannot be used.');
        }

        return $this->withStatus(QuoteStatus::USED);
    }

    public function expire(DateTimeImmutable $now): self
    {
        if ($this->status !== QuoteStatus::FROZEN) {
            throw new DomainException('Only a frozen quote can expire.');
        }

        if ($this->expiresAt === null || $now < $this->expiresAt) {
            throw new DomainException('Quote has not reached its expiration time.');
        }

        return $this->withStatus(QuoteStatus::EXPIRED);
    }

    public function cancel(): self
    {
        if (! in_array($this->status, [QuoteStatus::REQUESTED, QuoteStatus::FROZEN], true)) {
            throw new DomainException('Only requested or frozen quotes can be cancelled.');
        }

        return $this->withStatus(QuoteStatus::CANCELLED);
    }

    private function withStatus(QuoteStatus $status): self
    {
        return new self(
            $this->id,
            $this->scope,
            $this->traceId,
            $this->correlationId,
            $this->idempotencyKey,
            $status,
            $this->requestedAt,
            $this->expiresAt,
        );
    }

    public function id(): QuoteId { return $this->id; }
    public function scope(): FinancialScope { return $this->scope; }
    public function traceId(): TraceId { return $this->traceId; }
    public function correlationId(): CorrelationId { return $this->correlationId; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function status(): QuoteStatus { return $this->status; }
    public function requestedAt(): DateTimeImmutable { return $this->requestedAt; }
    public function expiresAt(): ?DateTimeImmutable { return $this->expiresAt; }
}
