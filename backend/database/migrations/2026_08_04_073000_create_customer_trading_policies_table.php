<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_trading_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_group_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('requires_available_balance')->default(true);
            $table->boolean('allow_negative_balance')->default(false);
            $table->unsignedInteger('asset_lock_minutes')->default(1440);
            $table->decimal('max_gold_weight', 24, 8)->nullable();
            $table->unsignedInteger('max_coin_quantity')->nullable();
            $table->decimal('max_money_amount', 24, 2)->nullable();
            $table->decimal('credit_limit', 24, 2)->nullable();
            $table->decimal('min_order_amount', 24, 2)->nullable();
            $table->decimal('max_order_amount', 24, 2)->nullable();
            $table->unsignedInteger('max_delivery_items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_trading_policies');
    }
};
