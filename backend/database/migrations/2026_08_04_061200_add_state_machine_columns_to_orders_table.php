<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('executing_at')->nullable();
            $table->timestamp('settling_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->text('status_reason')->nullable();
            $table->unsignedInteger('state_version')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'expires_at',
                'approved_at',
                'executing_at',
                'settling_at',
                'completed_at',
                'rejected_at',
                'cancelled_at',
                'failed_at',
                'expired_at',
                'status_reason',
                'state_version',
            ]);
        });
    }
};
