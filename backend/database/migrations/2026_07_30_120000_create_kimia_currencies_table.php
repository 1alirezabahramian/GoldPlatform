<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kimia_currencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kimia_id')->unique();
            $table->string('name')->nullable();
            $table->boolean('is_visible')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kimia_currencies');
    }
};
