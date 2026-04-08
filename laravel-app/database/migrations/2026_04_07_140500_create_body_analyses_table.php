<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('body_analyses');
        Schema::create('body_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('photo_path');
            $table->string('view_type', 20)->default('front'); // front, back, side
            $table->json('landmarks')->nullable(); // Coordenadas do MediaPipe
            $table->json('metrics')->nullable(); // Assimetrias calculadas, ângulos, etc.
            $table->text('ai_summary')->nullable(); // Sugestões de treino/dieta geradas
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_analyses');
    }
};
