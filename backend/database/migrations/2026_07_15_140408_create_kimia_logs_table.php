<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kimia_logs', function (Blueprint $table) {

            $table->id();

            $table->string('service');

            $table->string('method');

            $table->longText('request')->nullable();

            $table->longText('response')->nullable();

            $table->integer('http_code')->nullable();

            $table->boolean('success')->default(false);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kimia_logs');
    }
};