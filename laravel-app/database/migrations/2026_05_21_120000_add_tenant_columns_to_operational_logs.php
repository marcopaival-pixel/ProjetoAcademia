<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'workout_import_logs',
            'system_access_links',
            'audit_logs',
            'record_versions',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'clinic_id')) {
                    $table->unsignedBigInteger('clinic_id')->nullable()->after('user_id')->index();
                }
                if (! Schema::hasColumn($tableName, 'academy_company_id')) {
                    $table->unsignedBigInteger('academy_company_id')->nullable()->after('clinic_id')->index();
                }
            });
        }

    }

    public function down(): void
    {
        $tables = [
            'workout_import_logs',
            'system_access_links',
            'audit_logs',
            'record_versions',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'academy_company_id')) {
                    $table->dropColumn('academy_company_id');
                }
                if (Schema::hasColumn($tableName, 'clinic_id')) {
                    $table->dropColumn('clinic_id');
                }
            });
        }
    }
};
