<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->decimal('bf_percent', 5, 2)->nullable(); // Gordura Corporal
            $table->decimal('muscle_percent', 5, 2)->nullable(); // Massa Muscular
            
            // Medidas (cm)
            $table->decimal('neck', 5, 2)->nullable();
            $table->decimal('chest', 5, 2)->nullable();
            $table->decimal('waist', 5, 2)->nullable();
            $table->decimal('abdomen', 5, 2)->nullable();
            $table->decimal('hips', 5, 2)->nullable();
            
            $table->decimal('bicep_l', 5, 2)->nullable();
            $table->decimal('bicep_r', 5, 2)->nullable();
            $table->decimal('forearm_l', 5, 2)->nullable();
            $table->decimal('forearm_r', 5, 2)->nullable();
            
            $table->decimal('thigh_l', 5, 2)->nullable();
            $table->decimal('thigh_r', 5, 2)->nullable();
            $table->decimal('calf_l', 5, 2)->nullable();
            $table->decimal('calf_r', 5, 2)->nullable();
            
            $table->date('assessment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_assessments');
    }
};
