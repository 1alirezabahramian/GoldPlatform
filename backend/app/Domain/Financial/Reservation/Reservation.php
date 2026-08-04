<?php

namespace App\Domain\Financial\Reservation;

use App\Domain\Financial\Enums\ReservationStatus;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\ReservationId;
use App\Domain\Financial\ValueObjects\TraceId;
use DomainException;
use InvalidArgumentException;

final readonly class Reservation
{
    public function __construct(
        private ReservationId $id,
        private LedgerAccountId $accountId,
        private FinancialAssetId $assetId,
        private ExactDecimal $amount,
        private TraceId $traceId,
        private CorrelationId $correlationId,
        private IdempotencyKey $idempotencyKey,
        private ReservationStatus $status = ReservationStatus::ACTIVE,
    ) {
        if ($amount->compare(ExactDecimal::fromString('0')) <= 0) {
            throw new InvalidArgumentException('Reservation amount must be positive.');
        }
    }

    public function id(): ReservationId { return $this->id; }
    public function accountId(): LedgerAccountId { return $this->accountId; }
    public function assetId(): FinancialAssetId { return $this->assetId; }
    public function amount(): ExactDecimal { return $this->amount; }
    public function traceId(): TraceId { return $this->traceId; }
    public function correlationId(): CorrelationId { return $this->correlationId; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function status(): ReservationStatus { return $this->status; }

    public function release(TraceId $traceId, IdempotencyKey $idempotencyKey): self
    {
        return $this->transition(ReservationStatus::RELEASED, $traceId, $idempotencyKey);
    }

    public function capture(TraceId $traceId, IdempotencyKey $idempotencyKey): self
    {
        return $this->transition(ReservationStatus::CAPTURED, $traceId, $idempotencyKey);
    }

    public function expire(TraceId $traceId, IdempotencyKey $idempotencyKey): self
    {
        return $this->transition(ReservationStatus::EXPIRED, $traceId, $idempotencyKey);
    }

    private function transition(
        ReservationStatus $status,
        TraceId $traceId,
        IdempotencyKey $idempotencyKey,
    ): self {
        if ($this->status !== ReservationStatus::ACTIVE) {
            throw new DomainException('Only an active reservation can transition.');
        }

        return new self(
            id: $this->id,
            accountId: $this->accountId,
            assetId: $this->assetId,
            amount: $this->amount,
            traceId: $traceId,
            correlationId: $this->correlationId,
            idempotencyKey: $idempotencyKey,
            status: $status,
        );
    }
}
