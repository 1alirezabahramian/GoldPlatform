<?php

namespace App\Integrations\Kimia\Write;

final readonly class KimiaWritePlan
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public string $operation,
        public string $method,
        public string $uri,
        public string $idempotencyKey,
        public string $payloadHash,
        public ?string $compensationOperation,
        public array $payload,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function auditContext(): array
    {
        return [
            'operation' => $this->operation,
            'method' => $this->method,
            'uri' => $this->uri,
            'idempotency_key' => $this->idempotencyKey,
            'payload_hash' => $this->payloadHash,
            'compensation_operation' => $this->compensationOperation,
        ];
    }
}
