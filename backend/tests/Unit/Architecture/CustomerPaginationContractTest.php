<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerPaginationContractTest extends TestCase
{
    #[Test]
    public function pagination_request_has_safe_defaults_and_limits(): void
    {
        $request = (string) file_get_contents(app_path('Http/Requests/Api/V1/CustomerPaginationRequest.php'));

        self::assertStringContainsString("'per_page' => ['sometimes', 'integer', 'min:1', 'max:50']", $request);
        self::assertStringContainsString("validated('per_page', 25)", $request);
        self::assertStringContainsString("'page' => ['sometimes', 'integer', 'min:1']", $request);
    }

    #[Test]
    public function customer_lists_share_the_same_pagination_request(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        self::assertSame(3, substr_count($controller, 'CustomerPaginationRequest $request'));
        self::assertSame(3, substr_count($controller, 'paginate($request->perPage())'));
        self::assertStringNotContainsString('paginate(25)', $controller);
    }

    #[Test]
    public function pagination_meta_remains_stable_for_frontend(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        foreach (['current_page', 'per_page', 'total', 'last_page', 'has_more'] as $field) {
            self::assertStringContainsString("'{$field}'", $controller);
        }
    }
}
