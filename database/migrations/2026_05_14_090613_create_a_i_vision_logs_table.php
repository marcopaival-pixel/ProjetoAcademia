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
        Schema::create('ai_vision_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orchestrator_log_id')->index()->nullable();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('clinic_id')->index()->nullable();
            
            $table->string('document_type')->index(); // workout_sheet, meal_photo, etc
            $table->float('confidence')->default(0);
            $table->string('image_path')->nullable();
            
            $table->json('extracted_data')->nullable();
            $table->json('warnings')->nullable();
            
            $table->string('model_name')->nullable();
            $table->integer('total_tokens')->default(0);
            $table->decimal('cost_usd', 10, 6)->default(0);
            $table->integer('execution_time_ms')->default(0);
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Nota: clinic_id não tem FK aqui para evitar problemas se clinics for deletada mas log mantido (padrão do projeto)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_i_vision_logs');
    }
};
