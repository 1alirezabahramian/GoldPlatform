<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Enums\OrderDecision;
use App\Domain\Trading\ValueObjects\OrderId;
use InvalidArgumentException;

final readonly class OrderDecisionCommand
{
    public function __construct(
        private FinancialScope $scope,
        private OrderId $orderId,
        private OrderDecision $decision,
        private TraceId $traceId,
        private IdempotencyKey $idempotencyKey,
        private string $requestHash,
        private ?string $rejectionReason = null,
    ) {
        if (trim($requestHash) === '') {
            throw new InvalidArgumentException('Order decision request hash is required.');
        }

        if ($decision === OrderDecision::REJECT && trim((string) $rejectionReason) === '') {
            throw new InvalidArgumentException('Reject decision requires a reason.');
        }
    }

    public function scope(): FinancialScope { return $this->scope; }
    public function orderId(): OrderId { return $this->orderId; }
    public function decision(): OrderDecision { return $this->decision; }
    public function traceId(): TraceId { return $this->traceId; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function requestHash(): string { return $this->requestHash; }
    public function rejectionReason(): ?string { return $this->rejectionReason; }
}
