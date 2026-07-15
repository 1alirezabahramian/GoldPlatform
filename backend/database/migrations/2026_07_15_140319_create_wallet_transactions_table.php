<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();

            $table->enum('wallet_type',['rial','gold']);

            $table->enum('type',['deposit','withdraw','buy','sell','refund','adjustment','block','unblock']);

            $table->decimal('amount',18,3);

            $table->decimal('balance_after',18,3);

            $table->string('reference')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};