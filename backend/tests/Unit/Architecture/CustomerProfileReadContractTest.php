<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerProfileReadContractTest extends TestCase
{
    #[Test]
    public function profile_route_is_versioned_and_customer_scoped(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));

        self::assertStringContainsString("Route::get('/profile', CustomerProfileController::class)", $routes);
        self::assertStringContainsString("Route::prefix('v1/customer')->middleware('role:customer')", $routes);
    }

    #[Test]
    public function profile_response_exposes_only_customer_safe_fields(): void
    {
        $controller = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerProfileController.php')
        );

        foreach ([
            "'first_name'",
            "'last_name'",
            "'display_name'",
            "'mobile'",
            "'mobile_verified'",
            "'is_active'",
            "'roles'",
            "'last_login_at'",
        ] as $allowed) {
            self::assertStringContainsString($allowed, $controller);
        }

        foreach ([
            "'password'",
            "'remember_token'",
            "'account_id'",
            "'group_id'",
            "'national_code'",
            "'email'",
            "'tokens'",
            "'personal_access_tokens'",
        ] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $controller);
        }
    }

    #[Test]
    public function profile_uses_standard_customer_envelope(): void
    {
        $controller = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerProfileController.php')
        );

        self::assertStringContainsString('CustomerApiResponse::success', $controller);
        self::assertStringNotContainsString('return response()->json($user)', $controller);
        self::assertStringNotContainsString('toArray()', $controller);
    }
}
