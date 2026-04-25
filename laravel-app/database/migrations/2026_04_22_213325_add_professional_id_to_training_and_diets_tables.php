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
            if (!Schema::hasColumn('training_plans', 'professional_id')) {
                $table->unsignedInteger('professional_id')->nullable()->after('user_id');
                $table->foreign('professional_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('meal_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('meal_templates', 'professional_id')) {
                $table->unsignedInteger('professional_id')->nullable()->after('user_id');
                $table->foreign('professional_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_templates', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');
        });

        Schema::table('training_plans', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');
        });
    }
};
