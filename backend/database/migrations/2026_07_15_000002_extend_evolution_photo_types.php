<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE evolution_photos MODIFY type ENUM('front', 'side', 'right_side', 'left_side', 'back', 'custom') DEFAULT 'front'");
    }

    public function down(): void
    {
        DB::table('evolution_photos')
            ->whereIn('type', ['right_side', 'left_side'])
            ->update(['type' => 'side']);

        DB::statement("ALTER TABLE evolution_photos MODIFY type ENUM('front', 'side', 'back', 'custom') DEFAULT 'front'");
    }
};
