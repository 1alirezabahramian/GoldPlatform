<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('rial_balance',18,0)->default(0);

            $table->decimal('gold_balance',18,3)->default(0);

            $table->decimal('blocked_rial',18,0)->default(0);

            $table->decimal('blocked_gold',18,3)->default(0);

            $table->timestamps();

            $table->unique('user_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};