<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // این Migration دیگر مورد نیاز نیست.
        // ستون name از ابتدا nullable است و ستون email در GoldPlatform وجود ندارد.
    }

    public function down(): void
    {
        //
    }
};