<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateAccountId = DB::table('users')
            ->select('account_id')
            ->whereNotNull('account_id')
            ->groupBy('account_id')
            ->havingRaw('COUNT(*) > 1')
            ->value('account_id');

        if ($duplicateAccountId !== null) {
            throw new RuntimeException(
                'Cannot enforce one-to-one Kimia binding while duplicate account links exist.'
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('account_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_account_id_unique');
        });
    }
};
