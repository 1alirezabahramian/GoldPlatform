<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

final class FinancialKernelSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_financial_kernel_tables_and_required_scope_columns_exist(): void
    {
        $tables = [
            'financial_journals',
            'financial_journal_lines',
            'financial_events',
            'financial_idempotency_records',
            'financial_balance_projections',
        ];

        foreach ($tables as $table) {
            self::assertTrue(Schema::hasTable($table), "Missing financial table: {$table}");
        }

        foreach ([
            'financial_journals',
            'financial_events',
            'financial_idempotency_records',
            'financial_balance_projections',
        ] as $table) {
            self::assertTrue(Schema::hasColumns($table, [
                'scope_key',
                'tenant_id',
                'company_id',
                'branch_id',
            ]), "Missing scope columns on: {$table}");
        }
    }

    public function test_exact_decimal_values_are_persisted_without_a_guessed_numeric_scale(): void
    {
        $now = now();
        $traceId = (string) Str::uuid();
        $scopeKey = 'tenant:tenant-a:company:company-a:branch:branch-a';

        $journalId = DB::table('financial_journals')->insertGetId([
            'document_uuid' => (string) Str::uuid(),
            'scope_key' => $scopeKey,
            'tenant_id' => 'tenant-a',
            'company_id' => 'company-a',
            'branch_id' => 'branch-a',
            'trace_id' => $traceId,
            'correlation_id' => (string) Str::uuid(),
            'idempotency_key' => 'schema-test-journal',
            'status' => 'draft',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $exactAmount = '123456789012345678901234567890.12345678901234567890';

        DB::table('financial_journal_lines')->insert([
            'financial_journal_id' => $journalId,
            'ledger_account_id' => 'customer:1:money',
            'asset_type' => 'money',
            'asset_id' => 'rial',
            'side' => 'debit',
            'amount' => $exactAmount,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        self::assertSame(
            $exactAmount,
            DB::table('financial_journal_lines')
                ->where('financial_journal_id', $journalId)
                ->value('amount'),
        );
    }

    public function test_same_idempotency_key_is_isolated_by_financial_scope(): void
    {
        $now = now();
        $key = 'shared-request-key';

        foreach (['tenant-a', 'tenant-b'] as $tenantId) {
            DB::table('financial_idempotency_records')->insert([
                'scope_key' => "tenant:{$tenantId}:company:*:branch:*",
                'tenant_id' => $tenantId,
                'company_id' => null,
                'branch_id' => null,
                'idempotency_key' => $key,
                'operation' => 'financial.journal.post',
                'request_hash' => hash('sha256', $tenantId),
                'trace_id' => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        self::assertSame(
            2,
            DB::table('financial_idempotency_records')
                ->where('idempotency_key', $key)
                ->count(),
        );
    }
}
