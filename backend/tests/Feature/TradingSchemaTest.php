<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class TradingSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_trading_tables_and_scope_columns_exist(): void
    {
        self::assertTrue(Schema::hasTable('trading_quotes'));
        self::assertTrue(Schema::hasTable('trading_orders'));

        foreach (['trading_quotes', 'trading_orders'] as $table) {
            self::assertTrue(Schema::hasColumns($table, [
                'scope_key',
                'scope_hash',
                'tenant_id',
                'company_id',
                'branch_id',
                'trace_id',
                'correlation_id',
                'idempotency_key',
                'status',
            ]));
        }

        self::assertTrue(Schema::hasColumns('trading_quotes', ['quote_uuid', 'requested_at', 'expires_at']));
        self::assertTrue(Schema::hasColumns('trading_orders', [
            'order_uuid',
            'trading_quote_id',
            'created_at',
            'submitted_at',
            'rejection_reason',
        ]));
    }
}
