<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerApiErrorContractTest extends TestCase
{
    #[Test]
    public function customer_exception_mapping_is_scoped_to_v1_customer_routes(): void
    {
        $bootstrap = (string) file_get_contents(base_path('bootstrap/app.php'));

        self::assertStringContainsString("\$request->is('api/v1/customer/*')", $bootstrap);
        self::assertStringContainsString('return null;', $bootstrap);
    }

    #[Test]
    public function standard_customer_error_codes_are_explicit(): void
    {
        $bootstrap = (string) file_get_contents(base_path('bootstrap/app.php'));

        foreach ([
            'UNAUTHENTICATED',
            'FORBIDDEN',
            'RESOURCE_NOT_FOUND',
            'VALIDATION_FAILED',
            'RATE_LIMITED',
            'METHOD_NOT_ALLOWED',
            'INTERNAL_ERROR',
        ] as $code) {
            self::assertStringContainsString($code, $bootstrap);
        }
    }

    #[Test]
    public function errors_use_customer_envelope_and_do_not_return_exception_messages(): void
    {
        $bootstrap = (string) file_get_contents(base_path('bootstrap/app.php'));

        self::assertStringContainsString('CustomerApiResponse::error', $bootstrap);
        self::assertStringNotContainsString('$exception->getMessage()', $bootstrap);
        self::assertStringNotContainsString('$exception->getTrace', $bootstrap);
        self::assertStringNotContainsString('response()->json', $bootstrap);
    }

    #[Test]
    public function validation_errors_remain_structured_and_internal_errors_are_reported(): void
    {
        $bootstrap = (string) file_get_contents(base_path('bootstrap/app.php'));

        self::assertStringContainsString('$exception->errors()', $bootstrap);
        self::assertStringContainsString('report($exception);', $bootstrap);
    }
}
