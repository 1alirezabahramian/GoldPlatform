<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerStatusFilterContractTest extends TestCase
{
    #[Test]
    public function each_customer_list_uses_its_own_domain_enum_for_status_validation(): void
    {
        $expectations = [
            'CustomerOrderListRequest.php' => 'OrderStatus::class',
            'CustomerCustodyListRequest.php' => 'CustodyStatus::class',
            'CustomerDeliveryListRequest.php' => 'DeliveryStatus::class',
        ];

        foreach ($expectations as $file => $enum) {
            $request = (string) file_get_contents(app_path('Http/Requests/Api/V1/'.$file));

            self::assertStringContainsString("Rule::enum({$enum})", $request);
            self::assertStringContainsString("'status'", $request);
        }
    }

    #[Test]
    public function customer_read_queries_keep_ownership_before_status_filtering(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        self::assertSame(3, substr_count($controller, '->where(\'user_id\', $request->user()->id)'));
        self::assertSame(3, substr_count($controller, '->when($request->status()'));
        self::assertStringContainsString("'filters'", $controller);
        self::assertStringContainsString("'status' => \$status", $controller);
    }

    #[Test]
    public function filters_do_not_introduce_internal_or_kimia_identifiers(): void
    {
        $controller = strtolower((string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php')));

        self::assertStringNotContainsString('account_id', $controller);
        self::assertStringNotContainsString('product_id', $controller);
        self::assertStringNotContainsString('transaction_code', $controller);
        self::assertStringNotContainsString('action_code', $controller);
        self::assertStringNotContainsString('kimia', $controller);
    }
}
