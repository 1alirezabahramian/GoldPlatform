<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_accounts', function (Blueprint $table) {
            $table->id();

            // External provider
            $table->string('provider', 30);

            // Identity in external system
            $table->unsignedBigInteger('external_id');
            $table->string('code')->nullable();

            // Account information
            $table->string('name');
            $table->unsignedTinyInteger('type')->nullable();
            $table->string('mobile')->nullable();
            $table->string('national_id')->nullable();

            // External status
            $table->boolean('is_active')->default(true);

            // Synchronization timestamp
            $table->timestamp('last_synced_at')->nullable();

            $table->timestamps();

            // Constraints and indexes
            $table->unique(['provider', 'external_id']);
            $table->index(['provider', 'code']);
            $table->index('mobile');
            $table->index('national_id');
            $table->index('last_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_accounts');
    }
};