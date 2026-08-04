<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_quotes', function (Blueprint $table) {
            $table->id();
            $table->uuid('quote_uuid')->unique();
            $this->scope($table, 'trading_quotes_scope_index');
            $table->uuid('trace_id');
            $table->uuid('correlation_id');
            $table->string('idempotency_key', 191);
            $table->string('status', 32);
            $table->timestamp('requested_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['scope_hash', 'idempotency_key'], 'trading_quotes_scope_idempotency_unique');
            $table->index(['scope_hash', 'status'], 'trading_quotes_scope_status_index');
        });

        Schema::create('trading_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_uuid')->unique();
            $table->foreignId('trading_quote_id')->constrained('trading_quotes')->restrictOnDelete();
            $this->scope($table, 'trading_orders_scope_index');
            $table->uuid('trace_id');
            $table->uuid('correlation_id');
            $table->string('idempotency_key', 191);
            $table->string('status', 32);
            $table->timestamp('created_at');
            $table->timestamp('submitted_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['scope_hash', 'idempotency_key'], 'trading_orders_scope_idempotency_unique');
            $table->unique(['scope_hash', 'trading_quote_id'], 'trading_orders_scope_quote_unique');
            $table->index(['scope_hash', 'status'], 'trading_orders_scope_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_orders');
        Schema::dropIfExists('trading_quotes');
    }

    private function scope(Blueprint $table, string $indexName): void
    {
        $table->string('scope_key', 512);
        $table->char('scope_hash', 64);
        $table->string('tenant_id', 191);
        $table->string('company_id', 191)->nullable();
        $table->string('branch_id', 191)->nullable();
        $table->index(['tenant_id', 'company_id', 'branch_id'], $indexName);
    }
};
