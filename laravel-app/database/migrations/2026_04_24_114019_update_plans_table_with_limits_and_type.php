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
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'type')) {
                $table->string('type', 32)->change();
            }
            
            $table->integer('max_diets')->default(0)->after('max_workouts');
            $table->integer('max_assessments')->default(0)->after('max_diets');
            $table->integer('max_patients')->default(0)->after('max_assessments');
            $table->integer('max_professionals')->default(0)->after('max_patients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['max_diets', 'max_assessments', 'max_patients', 'max_professionals']);
            // Enum restoration depends on database driver, keeping as string is safer.
        });
    }
};
