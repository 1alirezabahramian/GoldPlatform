<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerOpenApiFoundationTest extends TestCase
{
    private function specification(): string
    {
        return (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));
    }

    #[Test]
    public function specification_is_openapi_31_and_customer_scoped(): void
    {
        $specification = $this->specification();

        self::assertStringContainsString('openapi: 3.1.0', $specification);
        self::assertStringContainsString('url: /api/v1/customer', $specification);
        self::assertStringContainsString('sanctum:', $specification);
    }

    #[Test]
    public function currently_implemented_customer_contracts_are_documented(): void
    {
        $specification = $this->specification();

        foreach ([
            '/bootstrap:',
            '/dashboard:',
            '/profile:',
            '/activities:',
            '/assets:',
            '/assets/money:',
            '/assets/gold:',
            '/assets/coins:',
            '/assets/currencies:',
            '/orders:',
            '/order-statuses:',
            '/custodies:',
            '/custodies/{reference}:',
            '/custodies/{reference}/delivery-request:',
            '/deliveries:',
            '/deliveries/{reference}:',
        ] as $path) {
            self::assertStringContainsString($path, $specification);
        }
    }

    #[Test]
    public function financial_values_are_strings_and_internal_kimia_ids_are_not_contract_fields(): void
    {
        $specification = $this->specification();

        self::assertStringContainsString("pattern: '^-?[0-9]+", $specification);
        self::assertStringNotContainsString('account_id:', $specification);
        self::assertStringNotContainsString('product_id:', $specification);
        self::assertStringNotContainsString('transaction_code:', $specification);
        self::assertStringNotContainsString('action_code:', $specification);
    }

    #[Test]
    public function public_references_trace_and_idempotency_are_explicit(): void
    {
        $specification = $this->specification();

        self::assertStringContainsString('PublicReference:', $specification);
        self::assertStringContainsString('format: uuid', $specification);
        self::assertStringContainsString('request_id:', $specification);
        self::assertStringContainsString('api_version:', $specification);
        self::assertStringContainsString('Idempotency-Key', $specification);
    }

    #[Test]
    public function standard_customer_error_contract_is_documented(): void
    {
        $specification = $this->specification();

        self::assertStringContainsString('ErrorEnvelope:', $specification);

        foreach ([
            'UNAUTHENTICATED',
            'FORBIDDEN',
            'RESOURCE_NOT_FOUND',
            'VALIDATION_FAILED',
            'RATE_LIMITED',
            'METHOD_NOT_ALLOWED',
            'INTERNAL_ERROR',
        ] as $code) {
            self::assertStringContainsString($code, $specification);
        }
    }
}
