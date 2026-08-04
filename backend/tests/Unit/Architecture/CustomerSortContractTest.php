<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerSortContractTest extends TestCase
{
    #[Test]
    public function sorting_is_whitelisted_and_defaults_to_newest(): void
    {
        $request = (string) file_get_contents(app_path('Http/Requests/Api/V1/CustomerPaginationRequest.php'));

        self::assertStringContainsString("'sort' => ['sometimes', 'string', 'in:newest,oldest']", $request);
        self::assertStringContainsString("validated('sort', 'newest')", $request);
        self::assertStringContainsString("return $this->sort() === 'oldest' ? 'asc' : 'desc';", $request);
    }

    #[Test]
    public function all_customer_lists_use_the_same_safe_sort_direction(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        self::assertSame(3, substr_count($controller, "->orderBy('id', $request->sortDirection())"));
        self::assertStringNotContainsString('orderByRaw', $controller);
        self::assertStringNotContainsString("$request->input('sort')", $controller);
    }

    #[Test]
    public function active_sort_is_returned_in_response_meta(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        self::assertStringContainsString("'sort' => $sort", $controller);
        self::assertSame(3, substr_count($controller, '$request->sort()'));
    }
}
