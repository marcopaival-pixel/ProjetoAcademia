<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('muscle_group', 64)->index(); // Peito, Costas, etc.
            $table->string('equipment', 64)->nullable(); // Halteres, Máquina, Barra, etc.
            $table->string('difficulty', 24)->default('beginner'); // beginner, intermediate, advanced
            $table->text('instructions')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises_catalog');
    }
};
