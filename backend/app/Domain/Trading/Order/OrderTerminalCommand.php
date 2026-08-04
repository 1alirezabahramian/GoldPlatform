<?php

namespace App\Domain\Trading\Order;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Trading\ValueObjects\OrderId;
use DateTimeImmutable;

final readonly class OrderTerminalCommand
{
    public function __construct(
        private FinancialScope $scope,
        private OrderId $orderId,
        private OrderTerminalAction $action,
        private IdempotencyKey $idempotencyKey,
        private string $requestHash,
        private DateTimeImmutable $occurredAt,
    ) {
        if (trim($requestHash) === '') {
            throw new \InvalidArgumentException('Order terminal command request hash is required.');
        }
    }

    public function scope(): FinancialScope { return $this->scope; }
    public function orderId(): OrderId { return $this->orderId; }
    public function action(): OrderTerminalAction { return $this->action; }
    public function idempotencyKey(): IdempotencyKey { return $this->idempotencyKey; }
    public function requestHash(): string { return $this->requestHash; }
    public function occurredAt(): DateTimeImmutable { return $this->occurredAt; }
}
