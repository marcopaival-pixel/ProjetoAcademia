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
        Schema::create('active_rest_routines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('duration');
            $table->string('intensity');
            $table->string('thumbnail')->nullable();
            $table->string('guide_image')->nullable();
            $table->string('video_id')->nullable();
            $table->text('benefit');
            $table->boolean('is_premium')->default(false);
            $table->json('exercises'); // Lista de nomes dos exercícios
            $table->json('execution_steps'); // Instruções passo a passo
            $table->json('tips')->nullable();
            $table->json('common_errors')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_rest_routines');
    }
};
