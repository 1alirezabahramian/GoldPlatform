<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('type', ['buy', 'sell']);
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->decimal('gold_weight', 24, 6)->nullable();
            $table->decimal('gold_price', 24, 2)->nullable();
            $table->decimal('commission', 24, 2)->default(0);
            $table->decimal('total_price', 24, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};