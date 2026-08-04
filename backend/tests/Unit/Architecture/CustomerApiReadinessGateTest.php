<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerApiReadinessGateTest extends TestCase
{
    #[Test]
    public function customer_v1_routes_keep_the_required_security_and_read_contracts(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));

        self::assertStringContainsString("Route::prefix('v1/customer')->middleware(['role:customer', 'customer.no-store'])", $routes);

        foreach ([
            "Route::get('/bootstrap'",
            "Route::get('/dashboard'",
            "Route::get('/activities'",
            "Route::get('/assets'",
            "Route::get('/orders'",
            "Route::get('/custodies'",
            "Route::get('/deliveries'",
            "Route::get('/profile'",
        ] as $route) {
            self::assertStringContainsString($route, $routes);
        }
    }

    #[Test]
    public function customer_contract_keeps_pagination_filter_sort_and_date_guards(): void
    {
        $request = (string) file_get_contents(app_path('Http/Requests/Api/V1/CustomerPaginationRequest.php'));

        foreach ([
            "'per_page' => ['sometimes', 'integer', 'min:1', 'max:50']",
            "'sort' => ['sometimes', 'string', 'in:newest,oldest']",
            "'from' => ['sometimes', 'date_format:Y-m-d']",
            "'to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:from']",
        ] as $guard) {
            self::assertStringContainsString($guard, $request);
        }
    }

    #[Test]
    public function customer_responses_keep_traceability_and_no_store_protection(): void
    {
        $response = (string) file_get_contents(app_path('Support/CustomerApiResponse.php'));
        $middleware = (string) file_get_contents(app_path('Http/Middleware/CustomerNoStore.php'));

        self::assertSame(2, substr_count($response, "->header('X-Request-ID'"));
        self::assertStringContainsString('private, no-store', $middleware);
        self::assertStringContainsString("'Pragma', 'no-cache'", $middleware);
        self::assertStringContainsString("'Expires', '0'", $middleware);
    }

    #[Test]
    public function openapi_and_error_contract_files_remain_available(): void
    {
        self::assertFileExists(base_path('../docs/api/customer-v1.openapi.yaml'));
        self::assertFileExists(app_path('Support/CustomerApiResponse.php'));

        $openApi = (string) file_get_contents(base_path('../docs/api/customer-v1.openapi.yaml'));

        self::assertStringContainsString('openapi: 3.1.0', $openApi);
        self::assertStringContainsString('/api/v1/customer/bootstrap:', $openApi);
        self::assertStringContainsString('/api/v1/customer/profile:', $openApi);
        self::assertStringContainsString('/api/v1/customer/activities:', $openApi);
    }
}
