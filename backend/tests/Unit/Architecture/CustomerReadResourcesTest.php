<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerReadResourcesTest extends TestCase
{
    #[Test]
    public function customer_read_controller_uses_explicit_resources(): void
    {
        $controller = (string) file_get_contents(app_path('Http/Controllers/Api/V1/CustomerReadController.php'));

        foreach ([
            'OrderResource::collection',
            'CustodyResource::collection',
            'DeliveryResource::collection',
        ] as $resourceUsage) {
            self::assertStringContainsString($resourceUsage, $controller);
        }

        self::assertStringNotContainsString('CustomerReadPresenter', $controller);
    }

    #[Test]
    public function customer_resources_do_not_publish_internal_or_operator_fields(): void
    {
        $paths = [
            app_path('Http/Resources/Api/V1/Customer/OrderResource.php'),
            app_path('Http/Resources/Api/V1/Customer/CustodyResource.php'),
            app_path('Http/Resources/Api/V1/Customer/DeliveryResource.php'),
        ];

        $contents = implode("\n", array_map(
            static fn (string $path): string => (string) file_get_contents($path),
            $paths,
        ));

        foreach ([
            "'user_id'",
            "'external_asset_id'",
            "'approved_by'",
            "'delivered_by'",
            "'receiver_identifier'",
            "'metadata'",
            "'kimia'",
            "'voucher_id'",
            "'record_id'",
        ] as $forbiddenField) {
            self::assertStringNotContainsString($forbiddenField, $contents);
        }
    }

    #[Test]
    public function customer_financial_quantities_are_serialized_as_strings(): void
    {
        $order = (string) file_get_contents(app_path('Http/Resources/Api/V1/Customer/OrderResource.php'));
        $custody = (string) file_get_contents(app_path('Http/Resources/Api/V1/Customer/CustodyResource.php'));

        self::assertStringContainsString('(string) $this->asset_quantity', $order);
        self::assertStringContainsString('(string) $this->quantity', $custody);
        self::assertStringContainsString('(string) $this->weight', $custody);
        self::assertStringContainsString('(string) $this->fineness', $custody);
    }
}
