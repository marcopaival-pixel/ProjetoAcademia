<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_vision_logs')) {
            return;
        }

        Schema::table('ai_vision_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_vision_logs', 'academy_company_id')) {
                $table->unsignedBigInteger('academy_company_id')->nullable()->after('clinic_id')->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ai_vision_logs')) {
            return;
        }

        Schema::table('ai_vision_logs', function (Blueprint $table) {
            if (Schema::hasColumn('ai_vision_logs', 'academy_company_id')) {
                $table->dropColumn('academy_company_id');
            }
        });
    }
};
