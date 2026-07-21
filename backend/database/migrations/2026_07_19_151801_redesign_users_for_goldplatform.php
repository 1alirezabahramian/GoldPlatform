<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('status')
                ->default('pending')
                ->after('mobile_verified');

            $table->timestamp('otp_verified_at')
                ->nullable()
                ->after('status');

            $table->timestamp('approved_at')
                ->nullable()
                ->after('otp_verified_at');

            $table->timestamp('terms_accepted_at')
                ->nullable()
                ->after('approved_at');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([

                'status',

                'otp_verified_at',

                'approved_at',

                'terms_accepted_at',

            ]);

        });
    }
};