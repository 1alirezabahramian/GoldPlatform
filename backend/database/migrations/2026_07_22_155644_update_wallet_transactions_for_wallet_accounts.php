<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {

            $table->dropForeign(['wallet_id']);

            $table->dropColumn('wallet_id');

            $table->foreignId('wallet_account_id')
                ->after('id')
                ->constrained('wallet_accounts')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {

            $table->dropForeign(['wallet_account_id']);

            $table->dropColumn('wallet_account_id');

            $table->foreignId('wallet_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }
};