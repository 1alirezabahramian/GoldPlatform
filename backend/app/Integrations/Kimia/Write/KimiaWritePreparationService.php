<?php

namespace App\Integrations\Kimia\Write;

use App\Integrations\Kimia\Exceptions\KimiaException;

final class KimiaWritePreparationService
{
    /**
     * @param array<string, mixed> $payload
     */
    public function prepare(string $operation, string $idempotencyKey, array $payload): KimiaWritePlan
    {
        if (! (bool) config('kimia_write.enabled', false)) {
            throw new KimiaException('Kimia write preparation is disabled.');
        }

        if (trim($idempotencyKey) === '') {
            throw new KimiaException('Kimia write preparation requires an idempotency key.');
        }

        $definition = config("kimia_write.operations.{$operation}");

        if (! is_array($definition) || ($definition['approved'] ?? false) !== true) {
            throw new KimiaException('Kimia write operation is not approved.');
        }

        $method = strtoupper((string) ($definition['method'] ?? ''));
        $uri = (string) ($definition['uri'] ?? '');
        $required = array_values(array_filter((array) ($definition['required_fields'] ?? []), 'is_string'));

        if (! in_array($method, ['POST', 'PUT', 'DELETE'], true) || $uri === '') {
            throw new KimiaException('Kimia write operation definition is incomplete.');
        }

        $missing = array_values(array_filter(
            $required,
            fn (string $field): bool => ! array_key_exists($field, $payload)
        ));

        if ($missing !== []) {
            throw new KimiaException('Kimia write payload is missing approved required fields.');
        }

        $normalizedPayload = $this->normalize($payload);
        $encoded = json_encode($normalizedPayload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);

        return new KimiaWritePlan(
            operation: $operation,
            method: $method,
            uri: $uri,
            idempotencyKey: $idempotencyKey,
            payloadHash: hash('sha256', $encoded),
            compensationOperation: isset($definition['compensation_operation'])
                ? (string) $definition['compensation_operation']
                : null,
            payload: $normalizedPayload,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function normalize(array $payload): array
    {
        ksort($payload);

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->normalize($value);
            }
        }

        return $payload;
    }
}
