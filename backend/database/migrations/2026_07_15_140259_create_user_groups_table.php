<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_groups', function (Blueprint $table) {

            $table->id();

            $table->string('title',100);

            $table->decimal('buy_commission',5,2)->default(0);

            $table->decimal('sell_commission',5,2)->default(0);

            $table->decimal('discount_percent',5,2)->default(0);

            $table->integer('priority')->default(0);

            $table->boolean('is_active')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};