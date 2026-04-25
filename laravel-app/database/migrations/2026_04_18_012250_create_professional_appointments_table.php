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
        Schema::create('professional_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('professional_id');
            $table->unsignedInteger('patient_id');
            $table->dateTime('appointment_at');
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, no-show
            $table->string('service_type')->nullable(); // Consulta, Avaliação, Treino, etc.
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_appointments');
    }
};
