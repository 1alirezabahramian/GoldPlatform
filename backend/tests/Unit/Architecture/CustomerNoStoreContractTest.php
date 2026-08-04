<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerNoStoreContractTest extends TestCase
{
    #[Test]
    public function customer_v1_routes_use_dedicated_no_store_middleware(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));

        self::assertStringContainsString("Route::prefix('v1/customer')->middleware(['role:customer', 'customer.no-store'])", $routes);
        self::assertSame(1, substr_count($routes, 'customer.no-store'));
    }

    #[Test]
    public function middleware_sets_private_no_store_headers(): void
    {
        $middleware = (string) file_get_contents(app_path('Http/Middleware/CustomerNoStore.php'));

        self::assertStringContainsString('private, no-store, no-cache, must-revalidate, max-age=0', $middleware);
        self::assertStringContainsString("'Pragma', 'no-cache'", $middleware);
        self::assertStringContainsString("'Expires', '0'", $middleware);
        self::assertStringContainsString("'Vary', 'Authorization'", $middleware);
    }

    #[Test]
    public function no_store_middleware_is_registered_without_global_scope(): void
    {
        $bootstrap = (string) file_get_contents(base_path('bootstrap/app.php'));

        self::assertStringContainsString("'customer.no-store' => CustomerNoStore::class", $bootstrap);
        self::assertStringNotContainsString('append(CustomerNoStore::class)', $bootstrap);
    }
}
