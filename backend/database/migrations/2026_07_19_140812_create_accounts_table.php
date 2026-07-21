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
        Schema::create('accounts', function (Blueprint $table) {

            $table->id();

            // Kimia
            $table->unsignedInteger('kimia_id')->unique();
            $table->unsignedInteger('account_code')->nullable();

            // Group
            $table->foreignId('group_id')
                ->nullable()
                ->constrained('account_groups')
                ->nullOnDelete();

            $table->unsignedTinyInteger('account_type')->nullable();

            // Identity
            $table->string('name')->nullable();
            $table->string('shop_name')->nullable();

            $table->string('national_code',20)
                ->nullable()
                ->index();

            $table->string('mobile',20)
                ->nullable()
                ->index();

            $table->string('tel')->nullable();

            // Business
            $table->string('economic_code',20)->nullable();

            // Address
            $table->text('address')->nullable();

            $table->string('postal_code',20)->nullable();

            // Other
            $table->dateTime('birthday')->nullable();

            $table->text('comment')->nullable();

            $table->boolean('is_visible')->default(true);

            // Sync
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->index('account_code');
            $table->index('group_id');
            $table->index('account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};