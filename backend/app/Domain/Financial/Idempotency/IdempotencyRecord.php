<?php

namespace App\Domain\Financial\Idempotency;

use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use InvalidArgumentException;

final readonly class IdempotencyRecord
{
    public function __construct(
        private IdempotencyKey $key,
        private string $operation,
        private string $requestHash,
        private TraceId $traceId,
        private ?string $resultReference = null,
    ) {
        if (trim($operation) === '' || trim($requestHash) === '') {
            throw new InvalidArgumentException('Idempotency operation and request hash are required.');
        }
    }

    public function key(): IdempotencyKey { return $this->key; }
    public function operation(): string { return $this->operation; }
    public function requestHash(): string { return $this->requestHash; }
    public function traceId(): TraceId { return $this->traceId; }
    public function resultReference(): ?string { return $this->resultReference; }

    public function matches(string $operation, string $requestHash): bool
    {
        return $this->operation === $operation && hash_equals($this->requestHash, $requestHash);
    }
}
