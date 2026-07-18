<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_groups', function (Blueprint $table) {

            $table->id();

            // شناسه گروه در Kimia
            $table->unsignedInteger('kimia_id')->unique();

            // نوع حساب
            $table->unsignedTinyInteger('account_type');

            // نام گروه
            $table->string('name');

            // وضعیت
            $table->boolean('is_active')->default(true);

            // آخرین زمان همگام‌سازی
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->index('account_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_groups');
    }
};