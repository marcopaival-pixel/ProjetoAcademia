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
        Schema::table('training_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('training_plans', 'frequency')) {
                $table->unsignedSmallInteger('frequency')->nullable()->after('goal');
            }
            if (!Schema::hasColumn('training_plans', 'difficulty')) {
                $table->string('difficulty', 20)->nullable()->after('frequency');
            }
            if (!Schema::hasColumn('training_plans', 'estimated_duration')) {
                $table->unsignedSmallInteger('estimated_duration')->nullable()->after('difficulty');
            }
        });

        Schema::table('exercise_sets', function (Blueprint $table) {
            if (!Schema::hasColumn('exercise_sets', 'rpe_target')) {
                $table->unsignedSmallInteger('rpe_target')->nullable()->after('rest_seconds');
            }
            if (!Schema::hasColumn('exercise_sets', 'cadence')) {
                $table->string('cadence', 10)->nullable()->after('rpe_target');
            }
            if (!Schema::hasColumn('exercise_sets', 'set_type')) {
                $table->string('set_type', 20)->default('work')->after('cadence');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_plans', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'difficulty', 'estimated_duration']);
        });

        Schema::table('exercise_sets', function (Blueprint $table) {
            $table->dropColumn(['rpe_target', 'cadence', 'set_type']);
        });
    }
};
