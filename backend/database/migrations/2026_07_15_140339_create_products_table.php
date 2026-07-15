<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('kimia_product_id')->nullable();

            $table->foreignId('category_id')->constrained('product_categories');

            $table->string('title');

            $table->string('barcode')->nullable()->unique();

            $table->decimal('weight',10,3)->default(0);

            $table->integer('fineness')->default(750);

            $table->decimal('buy_price',18,0)->default(0);

            $table->decimal('sell_price',18,0)->default(0);

            $table->integer('stock')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};