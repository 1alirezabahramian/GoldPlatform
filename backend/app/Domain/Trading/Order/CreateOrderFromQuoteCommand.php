<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\ValueObjects\QuoteId;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class CreateOrderFromQuoteCommand
{
    public function __construct(
        private FinancialScope $scope,
        private QuoteId $quoteId,
        private TraceId $traceId,
        private IdempotencyKey $idempotencyKey,
        private string $requestHash,
        private DateTimeImmutable $usedAt,
        private DateTimeImmutable $createdAt,
    ) {
        if (trim($requestHash) === '') {
            throw new InvalidArgumentException('Order creation request hash is required.');
        }

        if ($createdAt < $usedAt) {
            throw new InvalidArgumentException('Order creation time cannot precede quote use time.');
        }
    }

    public function scope(): FinancialScope { return $this->scope; }
    public function quoteId(): QuoteId { return $this->quoteId; }
    public function traceId(): TraceId { return $this->traceId; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function requestHash(): string { return $this->requestHash; }
    public function usedAt(): DateTimeImmutable { return $this->usedAt; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
