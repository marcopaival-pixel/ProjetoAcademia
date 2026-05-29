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
            if (!Schema::hasColumn('training_plans', 'student_profile')) {
                $table->string('student_profile', 30)->nullable()->after('difficulty');
            }
            if (!Schema::hasColumn('training_plans', 'split_type')) {
                $table->string('split_type', 30)->nullable()->after('student_profile');
            }
            if (!Schema::hasColumn('training_plans', 'status')) {
                $table->string('status', 20)->default('Rascunho')->after('is_active');
            }
            if (!Schema::hasColumn('training_plans', 'days_of_week')) {
                $table->json('days_of_week')->nullable()->after('frequency');
            }
            if (!Schema::hasColumn('training_plans', 'is_template')) {
                $table->boolean('is_template')->default(false)->after('status');
            }
            if (!Schema::hasColumn('training_plans', 'total_volume')) {
                $table->decimal('total_volume', 12, 2)->default(0)->after('estimated_duration');
            }
            if (!Schema::hasColumn('training_plans', 'muscles_worked')) {
                $table->json('muscles_worked')->nullable()->after('total_volume');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_plans', function (Blueprint $table) {
            $table->dropColumn([
                'student_profile',
                'split_type',
                'status',
                'days_of_week',
                'is_template',
                'total_volume',
                'muscles_worked'
            ]);
        });
    }
};
