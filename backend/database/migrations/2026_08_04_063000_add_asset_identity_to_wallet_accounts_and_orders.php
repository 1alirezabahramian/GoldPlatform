<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_accounts', function (Blueprint $table): void {
            $table->string('asset_type', 32)->default('money')->after('title')->index();
            $table->unsignedBigInteger('external_asset_id')->nullable()->after('asset_type')->index();
            $table->string('unit', 32)->default('IRR')->after('external_asset_id');
            $table->unique(['wallet_id', 'asset_type', 'external_asset_id', 'unit'], 'wallet_asset_identity_unique');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->string('asset_type', 32)->default('gold')->after('type')->index();
            $table->unsignedBigInteger('external_asset_id')->nullable()->after('asset_type')->index();
            $table->decimal('asset_quantity', 24, 8)->nullable()->after('external_asset_id');
            $table->string('asset_unit', 32)->default('GOLD18')->after('asset_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['asset_type', 'external_asset_id', 'asset_quantity', 'asset_unit']);
        });

        Schema::table('wallet_accounts', function (Blueprint $table): void {
            $table->dropUnique('wallet_asset_identity_unique');
            $table->dropColumn(['asset_type', 'external_asset_id', 'unit']);
        });
    }
};
