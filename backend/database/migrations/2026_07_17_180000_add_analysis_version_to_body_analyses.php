<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('body_analyses', 'analysis_version')) {
                $table->string('analysis_version', 80)->nullable()->after('ai_summary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('body_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('body_analyses', 'analysis_version')) {
                $table->dropColumn('analysis_version');
            }
        });
    }
};
