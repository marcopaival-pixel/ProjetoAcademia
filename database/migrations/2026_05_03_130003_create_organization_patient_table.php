<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('organization_patient')) {
            Schema::create('organization_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('internal_code')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'patient_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_patient');
    }
};
