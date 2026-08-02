<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_national_code_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('national_code');
        });
    }

    public function down(): void
    {
        $duplicateNationalCode = DB::table('users')
            ->select('national_code')
            ->whereNotNull('national_code')
            ->groupBy('national_code')
            ->havingRaw('COUNT(*) > 1')
            ->value('national_code');

        if ($duplicateNationalCode !== null) {
            throw new RuntimeException(
                'Cannot restore national-code uniqueness while duplicate values exist.'
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_national_code_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('national_code');
        });
    }
};
