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
        Schema::create('paciente_profissional', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('paciente_id');
            $table->unsignedInteger('profissional_id');
            $table->timestamp('data_vinculo')->useCurrent();
            $table->enum('ativo', ['Sim', 'Não'])->default('Sim');
            $table->unsignedInteger('empresa_id')->nullable();
            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('profissional_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index for performance on filters
            $table->index(['profissional_id', 'paciente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente_profissional');
    }
};
