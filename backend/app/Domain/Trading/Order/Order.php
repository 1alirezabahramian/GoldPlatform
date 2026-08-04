<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Enums\OrderStatus;
use App\Domain\Trading\Enums\QuoteStatus;
use App\Domain\Trading\Quote\Quote;
use App\Domain\Trading\ValueObjects\OrderId;
use App\Domain\Trading\ValueObjects\QuoteId;
use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;

final readonly class Order
{
    private function __construct(
        private OrderId $id,
        private QuoteId $quoteId,
        private FinancialScope $scope,
        private TraceId $traceId,
        private CorrelationId $correlationId,
        private IdempotencyKey $idempotencyKey,
        private OrderStatus $status,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $submittedAt = null,
        private ?string $rejectionReason = null,
    ) {
        if ($status === OrderStatus::REJECTED && trim((string) $rejectionReason) === '') {
            throw new InvalidArgumentException('A rejected order requires a reason.');
        }
    }

    public static function draftFromUsedQuote(
        Quote $quote,
        TraceId $traceId,
        IdempotencyKey $idempotencyKey,
        DateTimeImmutable $createdAt,
    ): self {
        if ($quote->status() !== QuoteStatus::USED) {
            throw new DomainException('An order can only be created from a used quote.');
        }

        return new self(
            id: OrderId::generate(),
            quoteId: $quote->id(),
            scope: $quote->scope(),
            traceId: $traceId,
            correlationId: $quote->correlationId(),
            idempotencyKey: $idempotencyKey,
            status: OrderStatus::DRAFT,
            createdAt: $createdAt,
        );
    }

    public static function rehydrate(
        OrderId $id,
        QuoteId $quoteId,
        FinancialScope $scope,
        TraceId $traceId,
        CorrelationId $correlationId,
        IdempotencyKey $idempotencyKey,
        OrderStatus $status,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $submittedAt = null,
        ?string $rejectionReason = null,
    ): self {
        return new self(
            $id,
            $quoteId,
            $scope,
            $traceId,
            $correlationId,
            $idempotencyKey,
            $status,
            $createdAt,
            $submittedAt,
            $rejectionReason,
        );
    }

    public function submit(DateTimeImmutable $submittedAt): self
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new DomainException('Only a draft order can be submitted.');
        }

        if ($submittedAt < $this->createdAt) {
            throw new DomainException('Order submission cannot precede creation.');
        }

        return $this->withStatus(OrderStatus::SUBMITTED, submittedAt: $submittedAt);
    }

    public function approve(): self
    {
        if ($this->status !== OrderStatus::SUBMITTED) {
            throw new DomainException('Only a submitted order can be approved.');
        }

        return $this->withStatus(OrderStatus::APPROVED);
    }

    public function reject(string $reason): self
    {
        if ($this->status !== OrderStatus::SUBMITTED) {
            throw new DomainException('Only a submitted order can be rejected.');
        }

        $reason = trim($reason);
        if ($reason === '') {
            throw new InvalidArgumentException('Order rejection reason cannot be empty.');
        }

        return $this->withStatus(OrderStatus::REJECTED, rejectionReason: $reason);
    }

    public function expire(): self
    {
        if ($this->status !== OrderStatus::SUBMITTED) {
            throw new DomainException('Only a submitted order can expire.');
        }

        return $this->withStatus(OrderStatus::EXPIRED);
    }

    public function cancel(): self
    {
        if (! in_array($this->status, [OrderStatus::DRAFT, OrderStatus::SUBMITTED], true)) {
            throw new DomainException('Only draft or submitted orders can be cancelled.');
        }

        return $this->withStatus(OrderStatus::CANCELLED);
    }

    private function withStatus(
        OrderStatus $status,
        ?DateTimeImmutable $submittedAt = null,
        ?string $rejectionReason = null,
    ): self {
        return new self(
            $this->id,
            $this->quoteId,
            $this->scope,
            $this->traceId,
            $this->correlationId,
            $this->idempotencyKey,
            $status,
            $this->createdAt,
            $submittedAt ?? $this->submittedAt,
            $rejectionReason,
        );
    }

    public function id(): OrderId { return $this->id; }
    public function quoteId(): QuoteId { return $this->quoteId; }
    public function scope(): FinancialScope { return $this->scope; }
    public function traceId(): TraceId { return $this->traceId; }
    public function correlationId(): CorrelationId { return $this->correlationId; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function status(): OrderStatus { return $this->status; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
    public function submittedAt(): ?DateTimeImmutable { return $this->submittedAt; }
    public function rejectionReason(): ?string { return $this->rejectionReason; }
}
