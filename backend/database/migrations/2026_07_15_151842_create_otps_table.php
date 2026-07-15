<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {

            $table->id();

            $table->string('mobile', 11)->index();

            $table->string('otp', 6);

            $table->enum('purpose', [
                'login',
                'register',
                'reset_password'
            ])->default('login');

            $table->unsignedTinyInteger('attempts')->default(0);

            $table->boolean('verified')->default(false);

            $table->timestamp('expires_at');

            $table->timestamp('verified_at')->nullable();

            $table->string('ip_address',45)->nullable();

            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index([
                'mobile',
                'purpose'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};