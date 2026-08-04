<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerRequestIdHeaderContractTest extends TestCase
{
    #[Test]
    public function success_and_error_responses_share_the_same_request_id_header(): void
    {
        $response = (string) file_get_contents(app_path('Support/CustomerApiResponse.php'));

        self::assertSame(2, substr_count($response, "->header('X-Request-ID', \$requestId)"));
        self::assertSame(2, substr_count($response, "(string) \$request->attributes->get('request_id')"));
        self::assertStringContainsString("'request_id' => \$requestId", $response);
    }

    #[Test]
    public function request_id_is_not_generated_inside_the_response_contract(): void
    {
        $response = (string) file_get_contents(app_path('Support/CustomerApiResponse.php'));

        self::assertStringNotContainsString('Str::uuid', $response);
        self::assertStringNotContainsString('uniqid(', $response);
        self::assertStringNotContainsString('random_bytes(', $response);
    }
}
