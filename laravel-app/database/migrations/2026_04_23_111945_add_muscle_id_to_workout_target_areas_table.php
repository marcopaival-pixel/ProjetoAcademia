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
        Schema::table('workout_target_areas', function (Blueprint $table) {
            $table->foreignId('muscle_id')->nullable()->after('training_plan_id')->constrained('muscles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_target_areas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('muscle_id');
        });
    }
};
