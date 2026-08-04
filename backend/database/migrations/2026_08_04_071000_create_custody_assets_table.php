<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custody_assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('asset_type', 32);
            $table->unsignedBigInteger('external_product_id')->nullable();
            $table->string('product_code')->nullable();
            $table->string('title');
            $table->decimal('quantity', 24, 8)->default(1);
            $table->decimal('weight', 24, 8)->nullable();
            $table->decimal('fineness', 12, 4)->nullable();
            $table->string('barcode')->nullable()->index();
            $table->string('branch_code')->nullable()->index();
            $table->string('status', 32)->default('in_custody')->index();
            $table->timestamp('acquired_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['asset_type', 'external_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custody_assets');
    }
};
