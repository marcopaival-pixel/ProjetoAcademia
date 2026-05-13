<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'medical_evolutions',
            'medical_prescriptions',
            'medical_reports',
            'medical_certificates',
            'patient_documents',
            'patient_treatment_plans'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'clinic_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->unsignedBigInteger('clinic_id')->nullable()->after('id');
                    $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'medical_evolutions',
            'medical_prescriptions',
            'medical_reports',
            'medical_certificates',
            'patient_documents',
            'patient_treatment_plans'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'clinic_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['clinic_id']);
                    $table->dropColumn('clinic_id');
                });
            }
        }
    }
};
