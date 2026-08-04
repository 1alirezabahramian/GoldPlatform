<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerDateFilterContractTest extends TestCase
{
    #[Test]
    public function date_range_is_iso_validated_and_ordered(): void
    {
        $request = (string) file_get_contents(app_path('Http/Requests/Api/V1/CustomerPaginationRequest.php'));

        self::assertStringContainsString("'from' => ['sometimes', 'date_format:Y-m-d']", $request);
        self::assertStringContainsString("'to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:from']", $request);
    }

    #[Test]
    public function all_customer_lists_filter_only_on_created_at(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        self::assertSame(3, substr_count($controller, 'whereDate(\'created_at\', \'>=\', $from)'));
        self::assertSame(3, substr_count($controller, 'whereDate(\'created_at\', \'<=\', $to)'));
        self::assertStringNotContainsString('whereRaw', $controller);
    }

    #[Test]
    public function active_date_filters_are_returned_in_meta(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        self::assertStringContainsString("'from' => \$from", $controller);
        self::assertStringContainsString("'to' => \$to", $controller);
    }
}
