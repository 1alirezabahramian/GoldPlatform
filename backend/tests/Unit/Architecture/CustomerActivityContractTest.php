<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerActivityContractTest extends TestCase
{
    #[Test]
    public function activity_route_is_versioned_authenticated_and_customer_scoped(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));

        self::assertStringContainsString("Route::get('/activities', CustomerActivityController::class)", $routes);
        self::assertStringContainsString("Route::prefix('v1/customer')->middleware('role:customer')", $routes);
    }

    #[Test]
    public function activity_sources_are_owned_by_authenticated_customer(): void
    {
        $model = (string) file_get_contents(app_path('Services/CustomerActivityReadModel.php'));

        self::assertGreaterThanOrEqual(3, substr_count($model, "->where('user_id', \$user->id)"));
        self::assertStringContainsString("'order_status'", $model);
        self::assertStringContainsString("'custody_status'", $model);
        self::assertStringContainsString("'delivery_status'", $model);
    }

    #[Test]
    public function pagination_and_filter_bounds_are_explicit(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerActivityController.php'));
        $model = (string) file_get_contents(app_path('Services/CustomerActivityReadModel.php'));

        self::assertStringContainsString("'per_page' => ['nullable', 'integer', 'min:1', 'max:50']", $controller);
        self::assertStringContainsString('CustomerActivityReadModel::eventTypes()', $controller);
        self::assertStringContainsString('$perPage = min(50, max(1, $perPage));', $model);
    }

    #[Test]
    public function responses_continue_to_use_safe_presenter(): void
    {
        $model = (string) file_get_contents(app_path('Services/CustomerActivityReadModel.php'));
        $presenter = (string) file_get_contents(app_path('Support/CustomerReadPresenter.php'));

        self::assertStringContainsString('CustomerReadPresenter', $model);
        self::assertStringNotContainsString('toArray()', $model);
        self::assertStringNotContainsString("'user_id'", $presenter);
        self::assertStringNotContainsString("'metadata'", $presenter);
    }
}
