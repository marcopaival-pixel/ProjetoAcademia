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
            $table->json('sets_data')->nullable()->after('calories_burned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->dropColumn('sets_data');
        });
    }
};
