<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('body_analyses', 'vision_model')) {
                $table->string('vision_model', 80)->nullable()->after('analysis_version');
            }

            if (!Schema::hasColumn('body_analyses', 'vision_confidence')) {
                $table->decimal('vision_confidence', 5, 4)->nullable()->after('vision_model');
            }

            if (!Schema::hasColumn('body_analyses', 'vision_raw_payload')) {
                $table->json('vision_raw_payload')->nullable()->after('vision_confidence');
            }
        });
    }

    public function down(): void
    {
        Schema::table('body_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('body_analyses', 'vision_raw_payload')) {
                $table->dropColumn('vision_raw_payload');
            }

            if (Schema::hasColumn('body_analyses', 'vision_confidence')) {
                $table->dropColumn('vision_confidence');
            }

            if (Schema::hasColumn('body_analyses', 'vision_model')) {
                $table->dropColumn('vision_model');
            }
        });
    }
};
