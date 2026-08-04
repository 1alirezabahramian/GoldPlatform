<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerTraceHeaderContractTest extends TestCase
{
    #[Test]
    public function success_and_error_responses_expose_the_same_request_id_header(): void
    {
        $response = (string) file_get_contents(app_path('Support/CustomerApiResponse.php'));

        self::assertSame(2, substr_count($response, "header('X-Request-ID', $requestId)"));
        self::assertStringContainsString("'request_id' => $requestId", $response);
        self::assertStringNotContainsString("header('X-Request-ID', $request->header", $response);
    }

    #[Test]
    public function request_id_is_read_from_trusted_request_context(): void
    {
        $response = (string) file_get_contents(app_path('Support/CustomerApiResponse.php'));

        self::assertSame(2, substr_count($response, "attributes->get('request_id')"));
    }
}
