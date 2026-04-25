<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->unsignedTinyInteger('rpe')->nullable()->after('duration_min');
            $table->unsignedSmallInteger('rest_default')->nullable()->default(60)->after('rpe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->dropColumn(['rpe', 'rest_default']);
        });
    }
};
