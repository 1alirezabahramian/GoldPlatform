<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE trades MODIFY status VARCHAR(32) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE trades MODIFY status ENUM('pending','completed','failed') NOT NULL DEFAULT 'pending'");
    }
};
