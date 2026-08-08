<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('customer_auth_mode', 20)->default('otp')->after('is_active');
            $table->string('staff_auth_mode', 20)->default('password')->after('customer_auth_mode');
            $table->string('customer_registration_mode', 20)->default('manual')->after('staff_auth_mode');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 100)->nullable()->after('mobile');
            $table->boolean('must_change_password')->default(false)->after('password');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            $table->foreignId('referrer_user_id')
                ->nullable()
                ->after('account_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('referral_code', 40)->nullable()->after('referrer_user_id');

            $table->unique(['tenant_id', 'username'], 'users_tenant_username_unique');
            $table->unique(['tenant_id', 'referral_code'], 'users_tenant_referral_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_tenant_username_unique');
            $table->dropUnique('users_tenant_referral_code_unique');
            $table->dropConstrainedForeignId('referrer_user_id');
            $table->dropColumn([
                'username',
                'must_change_password',
                'password_changed_at',
                'referral_code',
            ]);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'customer_auth_mode',
                'staff_auth_mode',
                'customer_registration_mode',
            ]);
        });
    }
};
