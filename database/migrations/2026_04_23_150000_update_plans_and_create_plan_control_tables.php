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
        // Update plans table
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'ai_credits')) {
                $table->integer('ai_credits')->default(0)->after('price');
            }
            if (!Schema::hasColumn('plans', 'max_students')) {
                $table->integer('max_students')->default(0)->after('ai_credits');
            }
            if (!Schema::hasColumn('plans', 'max_workouts')) {
                $table->integer('max_workouts')->default(0)->after('max_students');
            }
            if (!Schema::hasColumn('plans', 'max_exercises_per_workout')) {
                $table->integer('max_exercises_per_workout')->default(0)->after('max_workouts');
            }
            if (!Schema::hasColumn('plans', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('max_workouts');
            }
        });

        // Update plan_features table
        Schema::table('plan_features', function (Blueprint $table) {
            if (!Schema::hasColumn('plan_features', 'feature_key')) {
                $table->string('feature_key')->after('plan_id');
            }
            if (!Schema::hasColumn('plan_features', 'is_enabled')) {
                $table->boolean('is_enabled')->default(true)->after('feature_key');
            }
            // Remove feature_name if it exists
            if (Schema::hasColumn('plan_features', 'feature_name')) {
                $table->dropColumn('feature_name');
            }
        });

        // Create user_plans table
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create ai_usage table
        Schema::create('ai_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('feature');
            $table->integer('credits_used')->default(1);
            $table->timestamps(); // includes created_at

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage');
        Schema::dropIfExists('user_plans');
        
        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropColumn(['feature_key', 'is_enabled']);
            $table->string('feature_name')->after('plan_id')->nullable();
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['ai_credits', 'max_students', 'max_workouts', 'max_exercises_per_workout', 'is_active']);
        });
    }
};
