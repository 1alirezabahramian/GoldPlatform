<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {

            $table->id();

            $table->string('mobile',20);

            $table->string('template')->nullable();

            $table->string('code',10)->nullable();

            $table->text('message')->nullable();

            $table->boolean('success')->default(false);

            $table->string('provider')->default('sms.ir');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};