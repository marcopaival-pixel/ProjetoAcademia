<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('load_logs', function (Blueprint $table) {
            $table->index('log_date');
            $table->index(['user_id', 'exercise_id']);
            $table->index(['weight_kg', 'reps_done']);
        });

        Schema::table('body_analyses', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('body_assessments', function (Blueprint $table) {
            $table->index('assessment_date');
        });
    }

    public function down(): void
    {
        Schema::table('load_logs', function (Blueprint $table) {
            $table->dropIndex(['log_date']);
            $table->dropIndex(['user_id', 'exercise_id']);
            $table->dropIndex(['weight_kg', 'reps_done']);
        });
    }
};
