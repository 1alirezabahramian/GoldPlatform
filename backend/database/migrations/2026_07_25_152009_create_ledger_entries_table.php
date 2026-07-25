<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {

            $table->id();

            $table->foreignId('financial_transaction_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('wallet_account_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('entry_type', [
                'debit',
                'credit'
            ]);

            $table->decimal('amount',24,6);

            $table->string('currency',20);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index('wallet_account_id');
            $table->index('financial_transaction_id');
            $table->index('entry_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};