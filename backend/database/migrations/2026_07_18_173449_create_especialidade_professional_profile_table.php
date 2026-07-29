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
        Schema::create('especialidade_professional_profile', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professional_profile_id');
            $table->unsignedBigInteger('especialidade_id');

            $table->foreign('professional_profile_id', 'fk_esp_prof_prof_id')
                  ->references('id')->on('professional_profiles')
                  ->onDelete('cascade');
            
            $table->foreign('especialidade_id', 'fk_esp_prof_esp_id')
                  ->references('id')->on('especialidades')
                  ->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['professional_profile_id', 'especialidade_id'], 'prof_prof_esp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidade_professional_profile');
    }
};
