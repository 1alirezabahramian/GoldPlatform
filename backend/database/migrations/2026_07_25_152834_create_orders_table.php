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

            $table->string('order_no')->unique();

            $table->enum('type',[
                'buy',
                'sell'
            ]);

            $table->enum('asset_type',[
                'gold18',
                'coin',
                'parsian',
                'bullion'
            ]);

            $table->decimal('quantity',24,6);

            $table->decimal('unit_price',24,2);

            $table->decimal('total_price',24,2);

            $table->enum('status',[
                'draft',
                'submitted',
                'processing',
                'completed',
                'cancelled'
            ])->default('draft');

            $table->timestamps();

            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};