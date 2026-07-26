<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->foreignId('financial_transaction_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('trade_no')->unique();

            $table->decimal('quantity',24,6);

            $table->decimal('unit_price',24,2);

            $table->decimal('total_amount',24,2);

            $table->enum('status',[
                'pending',
                'executed',
                'cancelled'
            ])->default('pending');

            $table->timestamp('executed_at')->nullable();

            $table->timestamps();

            $table->index('trade_no');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};