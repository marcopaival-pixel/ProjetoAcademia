<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_target_areas', function (Blueprint $table) {
            if (!Schema::hasColumn('workout_target_areas', 'photo_validation_status')) {
                $table->string('photo_validation_status')->nullable()->after('reference_photo_path');
            }

            if (!Schema::hasColumn('workout_target_areas', 'photo_validation_data')) {
                $table->json('photo_validation_data')->nullable()->after('photo_validation_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workout_target_areas', function (Blueprint $table) {
            if (Schema::hasColumn('workout_target_areas', 'photo_validation_data')) {
                $table->dropColumn('photo_validation_data');
            }

            if (Schema::hasColumn('workout_target_areas', 'photo_validation_status')) {
                $table->dropColumn('photo_validation_status');
            }
        });
    }
};
