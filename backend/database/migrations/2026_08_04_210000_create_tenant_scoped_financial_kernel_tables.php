<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_journals', function (Blueprint $table) {
            $table->id();
            $table->uuid('document_uuid')->unique();

            $this->addScopeColumns($table);

            $table->uuid('trace_id');
            $table->uuid('correlation_id');
            $table->string('idempotency_key', 191);
            $table->string('status', 32);
            $table->uuid('reversal_trace_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->unique(['scope_key', 'trace_id'], 'financial_journals_scope_trace_unique');
            $table->unique(['scope_key', 'idempotency_key'], 'financial_journals_scope_idempotency_unique');
            $table->index(['tenant_id', 'company_id', 'branch_id'], 'financial_journals_scope_index');
            $table->index('correlation_id');
            $table->index('status');
        });

        Schema::create('financial_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_journal_id')
                ->constrained('financial_journals')
                ->restrictOnDelete();

            $table->string('ledger_account_id', 191);
            $table->string('asset_type', 32);
            $table->string('asset_id', 191);
            $table->string('side', 16);

            // Exact decimal canonical string. Numeric scale remains a domain decision.
            $table->string('amount', 255);

            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(
                ['ledger_account_id', 'asset_type', 'asset_id'],
                'financial_journal_lines_account_asset_index',
            );
            $table->index(['financial_journal_id', 'side'], 'financial_journal_lines_journal_side_index');
        });

        Schema::create('financial_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid')->unique();

            $this->addScopeColumns($table);

            $table->string('name', 191);
            $table->uuid('trace_id');
            $table->uuid('correlation_id');
            $table->string('idempotency_key', 191);
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['scope_key', 'correlation_id'], 'financial_events_scope_correlation_index');
            $table->index(['scope_key', 'trace_id'], 'financial_events_scope_trace_index');
            $table->index(['tenant_id', 'company_id', 'branch_id'], 'financial_events_scope_index');
        });

        Schema::create('financial_idempotency_records', function (Blueprint $table) {
            $table->id();

            $this->addScopeColumns($table);

            $table->string('idempotency_key', 191);
            $table->string('operation', 191);
            $table->string('request_hash', 128);
            $table->uuid('trace_id');
            $table->string('result_reference', 191)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['scope_key', 'idempotency_key'],
                'financial_idempotency_scope_key_unique',
            );
            $table->index(['tenant_id', 'company_id', 'branch_id'], 'financial_idempotency_scope_index');
            $table->index(['scope_key', 'operation'], 'financial_idempotency_scope_operation_index');
        });

        Schema::create('financial_balance_projections', function (Blueprint $table) {
            $table->id();

            $this->addScopeColumns($table);

            $table->string('ledger_account_id', 191);
            $table->string('asset_type', 32);
            $table->string('asset_id', 191);

            // Exact decimal canonical strings. No asset scale is guessed here.
            $table->string('posted_amount', 255);
            $table->string('reserved_amount', 255);

            $table->unsignedBigInteger('version')->default(0);
            $table->timestamps();

            $table->unique(
                ['scope_key', 'ledger_account_id', 'asset_type', 'asset_id'],
                'financial_balance_scope_account_asset_unique',
            );
            $table->index(['tenant_id', 'company_id', 'branch_id'], 'financial_balance_scope_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_balance_projections');
        Schema::dropIfExists('financial_idempotency_records');
        Schema::dropIfExists('financial_events');
        Schema::dropIfExists('financial_journal_lines');
        Schema::dropIfExists('financial_journals');
    }

    private function addScopeColumns(Blueprint $table): void
    {
        $table->string('scope_key', 512);
        $table->string('tenant_id', 191);
        $table->string('company_id', 191)->nullable();
        $table->string('branch_id', 191)->nullable();
    }
};