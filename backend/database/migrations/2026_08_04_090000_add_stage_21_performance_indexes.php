<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'id'], 'orders_user_status_id_idx');
            $table->index(['status', 'id'], 'orders_status_id_idx');
        });

        Schema::table('custody_assets', function (Blueprint $table) {
            $table->index(['user_id', 'id'], 'custody_assets_user_id_idx');
        });

        Schema::table('delivery_requests', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'id'], 'delivery_requests_user_status_id_idx');
            $table->index(['status', 'id'], 'delivery_requests_status_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_requests', function (Blueprint $table) {
            $table->dropIndex('delivery_requests_user_status_id_idx');
            $table->dropIndex('delivery_requests_status_id_idx');
        });

        Schema::table('custody_assets', function (Blueprint $table) {
            $table->dropIndex('custody_assets_user_id_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_id_idx');
            $table->dropIndex('orders_status_id_idx');
        });
    }
};
