<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_sessions', function (Blueprint $table): void {
            if (! Schema::hasColumn('workout_sessions', 'training_plan_id')) {
                $table->foreignId('training_plan_id')->nullable()->after('user_id')->constrained('training_plans')->nullOnDelete();
            }

            if (! Schema::hasColumn('workout_sessions', 'status')) {
                $table->string('status', 20)->default('completed')->after('session_date')->index();
            }

            if (! Schema::hasColumn('workout_sessions', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('workout_sessions', 'ended_at')) {
                $table->timestamp('ended_at')->nullable()->after('started_at');
            }

            if (! Schema::hasColumn('workout_sessions', 'completion_percent')) {
                $table->unsignedTinyInteger('completion_percent')->default(0)->after('ended_at');
            }

            if (! Schema::hasColumn('workout_sessions', 'completed_exercise_ids')) {
                $table->json('completed_exercise_ids')->nullable()->after('completion_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workout_sessions', function (Blueprint $table): void {
            foreach (['completed_exercise_ids', 'completion_percent', 'ended_at', 'started_at', 'status'] as $column) {
                if (Schema::hasColumn('workout_sessions', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('workout_sessions', 'training_plan_id')) {
                $table->dropConstrainedForeignId('training_plan_id');
            }
        });
    }
};
