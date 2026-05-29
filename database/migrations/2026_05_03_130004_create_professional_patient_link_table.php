<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('organization_professional_patient')) {
            Schema::create('organization_professional_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->unsignedInteger('professional_id');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['organization_id', 'professional_id', 'patient_id'], 'org_prof_pat_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_professional_patient');
    }
};
