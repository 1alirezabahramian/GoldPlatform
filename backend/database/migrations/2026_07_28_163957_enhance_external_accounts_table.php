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
        Schema::table('external_accounts', function (Blueprint $table) {

            $table->string('sync_status', 20)
                ->default('synced')
                ->after('is_active');

            $table->text('sync_error')
                ->nullable()
                ->after('sync_status');

            $table->string('sync_hash', 64)
                ->nullable()
                ->after('sync_error');

            $table->json('raw_data')
                ->nullable()
                ->after('sync_hash');

            $table->softDeletes()
                ->after('updated_at');

            $table->index('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_accounts', function (Blueprint $table) {

            $table->dropIndex(['sync_status']);

            $table->dropSoftDeletes();

            $table->dropColumn([
                'sync_status',
                'sync_error',
                'sync_hash',
                'raw_data',
            ]);
        });
    }
};