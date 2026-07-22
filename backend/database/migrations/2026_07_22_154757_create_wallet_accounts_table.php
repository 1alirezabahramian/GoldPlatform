<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_accounts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('wallet_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('code',50);          // RIAL,GOLD18,USD,...

            $table->string('title',100);

            $table->decimal('balance',24,6)->default(0);

            $table->decimal('blocked_balance',24,6)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['wallet_id','code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_accounts');
    }
};