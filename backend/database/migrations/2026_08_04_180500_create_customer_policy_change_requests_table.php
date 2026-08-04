<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_policy_change_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_trading_policy_id')->constrained()->cascadeOnDelete();
            $table->json('proposed_changes');
            $table->string('status', 32)->index();
            $table->text('reason');
            $table->text('review_note')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->index(['customer_trading_policy_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_policy_change_requests');
    }
};
