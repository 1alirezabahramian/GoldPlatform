<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('mobile', 11)
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('national_code', 10)
                ->nullable()
                ->unique()
                ->after('name');

            $table->foreignId('group_id')
                ->nullable()
                ->after('national_code')
                ->constrained('user_groups')
                ->nullOnDelete();

            $table->boolean('mobile_verified')
                ->default(false)
                ->after('group_id');

            $table->boolean('is_active')
                ->default(true)
                ->after('mobile_verified');

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['group_id']);

            $table->dropColumn([
                'mobile',
                'national_code',
                'group_id',
                'mobile_verified',
                'is_active',
                'last_login_at',
            ]);
        });
    }
};