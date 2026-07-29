<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kimia_coins', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('kimia_id')->unique();
            $table->string('name')->nullable();
            $table->decimal('fineness', 10, 4)->nullable();
            $table->decimal('weight', 12, 4)->nullable();
            $table->unsignedTinyInteger('type');
            $table->boolean('is_visible')->nullable();

            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('is_visible');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kimia_coins');
    }
};
