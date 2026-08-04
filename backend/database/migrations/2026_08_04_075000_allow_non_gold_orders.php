<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE orders MODIFY gold_weight DECIMAL(24,6) NULL');
        DB::statement('ALTER TABLE orders MODIFY gold_price DECIMAL(24,2) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE orders MODIFY gold_weight DECIMAL(24,6) NOT NULL');
        DB::statement('ALTER TABLE orders MODIFY gold_price DECIMAL(24,2) NOT NULL');
    }
};
