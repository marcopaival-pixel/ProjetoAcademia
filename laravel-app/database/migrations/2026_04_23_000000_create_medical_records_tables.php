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
        // 1. Evolução / Atendimentos
        Schema::create('medical_evolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->dateTime('date');
            $table->string('type')->nullable(); // Presencial, Online, etc.
            $table->text('chief_complaint')->nullable();
            $table->text('assessment')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('conduct')->nullable();
            $table->text('observations')->nullable();
            $table->json('attachments')->nullable();
            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Laudos
        Schema::create('medical_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->string('title');
            $table->dateTime('date');
            $table->text('description')->nullable();
            $table->text('conclusion')->nullable();
            $table->text('observations')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('qr_code')->nullable();
            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Receitas
        Schema::create('medical_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->dateTime('date');
            $table->string('medicine');
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->text('observations')->nullable();
            $table->string('pdf_path')->nullable();
            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Atestados
        Schema::create('medical_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->dateTime('date');
            $table->string('reason');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('period')->nullable(); // e.g. "7 dias"
            $table->text('observations')->nullable();
            $table->string('pdf_path')->nullable();
            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 5. Histórico de Ações (Audit Log)
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('user_id'); // Who performed the action
            $table->string('action_type'); // create, edit, delete, download, etc.
            $table->string('module'); // evolution, report, prescription, certificate
            $table->text('description');
            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 6. Update pacientes table with medical profile fields
        Schema::table('pacientes', function (Blueprint $table) {
            if (!Schema::hasColumn('pacientes', 'main_diagnosis')) {
                $table->text('main_diagnosis')->nullable()->after('status');
            }
            if (!Schema::hasColumn('pacientes', 'important_notes')) {
                $table->text('important_notes')->nullable()->after('main_diagnosis');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['main_diagnosis', 'important_notes']);
        });
        Schema::dropIfExists('medical_histories');
        Schema::dropIfExists('medical_certificates');
        Schema::dropIfExists('medical_prescriptions');
        Schema::dropIfExists('medical_reports');
        Schema::dropIfExists('medical_evolutions');
    }
};
