<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar categoria (A/B/C/D) aos planos
        Schema::table('training_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('training_plans', 'plan_label')) {
                $table->string('plan_label', 10)->nullable()->after('name');
            }
            if (!Schema::hasColumn('training_plans', 'goal')) {
                $table->string('goal', 50)->nullable()->after('description');
            }
        });

        // Adicionar registro de falha muscular nos logs
        Schema::table('load_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('load_logs', 'to_failure')) {
                $table->boolean('to_failure')->default(false)->after('reps_done');
            }
        });
    }

    public function down(): void
    {
        Schema::table('training_plans', function (Blueprint $table) {
            $table->dropColumn(['plan_label', 'goal']);
        });

        Schema::table('load_logs', function (Blueprint $table) {
            $table->dropColumn('to_failure');
        });
    }
};
