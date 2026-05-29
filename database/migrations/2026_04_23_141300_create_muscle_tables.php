<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muscle_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('muscles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('muscle_groups')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // Primário, Secundário, Estabilizador
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exercise_muscles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained('exercises_catalog')->onDelete('cascade');
            $table->foreignId('muscle_id')->constrained('muscles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_muscles');
        Schema::dropIfExists('muscles');
        Schema::dropIfExists('muscle_groups');
    }
};
