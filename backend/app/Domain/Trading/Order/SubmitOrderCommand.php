<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\ValueObjects\OrderId;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class SubmitOrderCommand
{
    public function __construct(
        private FinancialScope $scope,
        private OrderId $orderId,
        private TraceId $traceId,
        private IdempotencyKey $idempotencyKey,
        private string $requestHash,
        private DateTimeImmutable $submittedAt,
    ) {
        if (trim($requestHash) === '') {
            throw new InvalidArgumentException('Order submission request hash is required.');
        }
    }

    public function scope(): FinancialScope { return $this->scope; }
    public function orderId(): OrderId { return $this->orderId; }
    public function traceId(): TraceId { return $this->traceId; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function requestHash(): string { return $this->requestHash; }
    public function submittedAt(): DateTimeImmutable { return $this->submittedAt; }
}
