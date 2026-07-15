<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jibit_logs', function (Blueprint $table) {

            $table->id();

            $table->string('national_code',20);

            $table->string('mobile',20)->nullable();

            $table->boolean('verified')->default(false);

            $table->longText('response')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jibit_logs');
    }
};