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
        Schema::dropIfExists('load_logs');
        Schema::dropIfExists('exercise_sets');
        Schema::dropIfExists('training_plan_exercises');
        Schema::dropIfExists('training_plans');

        // 1. Planos de Treino (Ex: Treino A - Peito e Tríceps)
        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // 2. Exercícios dentro de um Plano
        Schema::create('training_plan_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_plan_id')->constrained('training_plans')->cascadeOnDelete();
            $table->unsignedBigInteger('exercise_id'); // Referência ao exercises_catalog
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('exercise_id')->references('id')->on('exercises_catalog')->cascadeOnDelete();
        });

        // 3. Estrutura de Séries (Sets) desejadas para o exercício no plano
        Schema::create('exercise_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_plan_exercise_id')->constrained('training_plan_exercises')->cascadeOnDelete();
            $table->unsignedSmallInteger('set_number');
            $table->unsignedSmallInteger('reps_target')->nullable();
            $table->decimal('weight_target', 8, 2)->nullable();
            $table->unsignedSmallInteger('rest_seconds')->default(60);
            $table->timestamps();
        });

        // 4. Logs de Execução Real (Progressão de Carga)
        Schema::create('load_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreignId('training_plan_exercise_id')->constrained('training_plan_exercises')->cascadeOnDelete();
            $table->unsignedBigInteger('exercise_id'); // redundante para facilitar queries de evolução por ex.
            $table->date('log_date');
            $table->unsignedSmallInteger('set_number');
            $table->unsignedSmallInteger('reps_done');
            $table->decimal('weight_kg', 8, 2);
            $table->unsignedSmallInteger('rpe')->nullable(); // 1-10 rate of perceived exertion
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'exercise_id', 'log_date']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('exercise_id')->references('id')->on('exercises_catalog')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_logs');
        Schema::dropIfExists('exercise_sets');
        Schema::dropIfExists('training_plan_exercises');
        Schema::dropIfExists('training_plans');
    }
};
