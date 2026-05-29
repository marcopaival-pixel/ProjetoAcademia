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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('physical_level')->nullable(); // beginner, intermediate, advanced
            $table->string('experience_level')->nullable(); 
            $table->string('training_location')->nullable(); // gym, home, outdoor
            $table->string('cardio_frequency')->nullable();
            $table->integer('sleep_hours')->nullable();
            $table->integer('nutrition_quality')->nullable(); // 1-10
            $table->integer('available_daily_time_mins')->nullable();
            $table->text('fitness_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'physical_level',
                'experience_level',
                'training_location',
                'cardio_frequency',
                'sleep_hours',
                'nutrition_quality',
                'available_daily_time_mins',
                'fitness_notes'
            ]);
        });
    }
};
