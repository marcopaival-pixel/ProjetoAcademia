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
        // 1. Enhance Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type', 32)->nullable()->index()->after('academy_company_id');
            }
            if (!Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp', 32)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'admission_date')) {
                $table->date('admission_date')->nullable()->after('user_type');
            }
            if (!Schema::hasColumn('users', 'link_type')) {
                $table->string('link_type', 32)->nullable()->after('admission_date');
            }
            if (!Schema::hasColumn('users', 'clinic_role')) {
                $table->string('clinic_role', 64)->nullable()->after('link_type');
            }
            if (!Schema::hasColumn('users', 'sector')) {
                $table->string('sector', 64)->nullable()->after('clinic_role');
            }
        });

        // 2. Enhance Professional Profiles Table
        Schema::table('professional_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('professional_profiles', 'certifications')) {
                $table->text('certifications')->nullable()->after('education');
            }
            if (!Schema::hasColumn('professional_profiles', 'academy_unit_id')) {
                $table->foreignId('academy_unit_id')->nullable()->after('user_id')->constrained('academy_units')->nullOnDelete();
            }
            if (!Schema::hasColumn('professional_profiles', 'room')) {
                $table->string('room', 64)->nullable()->after('academy_unit_id');
            }
            if (!Schema::hasColumn('professional_profiles', 'internal_permissions')) {
                $table->json('internal_permissions')->nullable()->after('is_public');
            }
        });

        // 3. Enhance Pacientes (Pivot/Table)
        Schema::table('pacientes', function (Blueprint $table) {
            if (!Schema::hasColumn('pacientes', 'patient_type')) {
                $table->string('patient_type', 32)->nullable()->index()->after('user_id');
            }
            if (!Schema::hasColumn('pacientes', 'insurance_type')) {
                $table->string('insurance_type', 32)->nullable()->after('patient_type');
            }
            if (!Schema::hasColumn('pacientes', 'insurance_card_number')) {
                $table->string('insurance_card_number', 64)->nullable()->after('insurance_type');
            }
            if (!Schema::hasColumn('pacientes', 'insurance_expiry')) {
                $table->date('insurance_expiry')->nullable()->after('insurance_card_number');
            }
            if (!Schema::hasColumn('pacientes', 'responsible_legal')) {
                $table->string('responsible_legal', 120)->nullable()->after('insurance_expiry');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['patient_type', 'insurance_type', 'insurance_card_number', 'insurance_expiry', 'responsible_legal']);
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropForeign(['academy_unit_id']);
            $table->dropColumn(['certifications', 'academy_unit_id', 'room', 'internal_permissions']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'whatsapp', 'admission_date', 'link_type', 'clinic_role', 'sector']);
        });
    }
};
