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
        Schema::table('professional_profiles', function (Blueprint $table) {
            // Dados profissionais
            if (!Schema::hasColumn('professional_profiles', 'experience_years')) {
                $table->integer('experience_years')->nullable()->after('specialty');
            }
            if (!Schema::hasColumn('professional_profiles', 'education')) {
                $table->text('education')->nullable()->after('experience_years');
            }
            
            // Perfil público
            if (!Schema::hasColumn('professional_profiles', 'professional_photo_path')) {
                $table->string('professional_photo_path')->nullable()->after('about');
            }
            if (!Schema::hasColumn('professional_profiles', 'offered_services')) {
                $table->text('offered_services')->nullable()->after('professional_photo_path');
            }
            
            // Atendimento
            if (!Schema::hasColumn('professional_profiles', 'consultation_price')) {
                $table->decimal('consultation_price', 10, 2)->nullable()->after('service_types');
            }
            
            // Local de atendimento
            if (!Schema::hasColumn('professional_profiles', 'clinic_address')) {
                $table->string('clinic_address')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('professional_profiles', 'clinic_city')) {
                $table->string('clinic_city')->nullable()->after('clinic_address');
            }
            if (!Schema::hasColumn('professional_profiles', 'clinic_state')) {
                $table->string('clinic_state', 2)->nullable()->after('clinic_city');
            }
            
            // Agenda
            if (!Schema::hasColumn('professional_profiles', 'work_days')) {
                $table->json('work_days')->nullable()->after('clinic_state');
            }
            if (!Schema::hasColumn('professional_profiles', 'work_start_time')) {
                $table->time('work_start_time')->nullable()->after('work_days');
            }
            if (!Schema::hasColumn('professional_profiles', 'work_end_time')) {
                $table->time('work_end_time')->nullable()->after('work_start_time');
            }
            
            // Visibilidade
            if (!Schema::hasColumn('professional_profiles', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('work_end_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'experience_years',
                'education',
                'professional_photo_path',
                'offered_services',
                'consultation_price',
                'clinic_address',
                'clinic_city',
                'clinic_state',
                'work_days',
                'work_start_time',
                'work_end_time',
                'is_public'
            ]);
        });
    }
};
