<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_stacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('professional_id')->nullable();
            $table->string('name');
            $table->string('goal')->nullable(); // emagrecimento, hipertrofia, performance, saúde
            $table->string('target_audience')->nullable(); // aluno, paciente, atleta
            $table->string('responsible_type')->default('ia'); // ia, profissional
            $table->string('status')->default('ativo'); // ativo, pausado, concluído
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->float('adherence_rate')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('supplements', function (Blueprint $table) {
            $table->unsignedBigInteger('smart_stack_id')->nullable()->after('user_id');
            $table->string('frequency')->nullable()->after('unit'); // diário, pré-treino, pós-treino
            $table->integer('duration_days')->nullable()->after('frequency');
            $table->string('supplement_goal')->nullable()->after('duration_days');
            $table->text('observations')->nullable()->after('supplement_goal');

            $table->foreign('smart_stack_id')->references('id')->on('smart_stacks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('supplements', function (Blueprint $table) {
            $table->dropForeign(['smart_stack_id']);
            $table->dropColumn(['smart_stack_id', 'frequency', 'duration_days', 'supplement_goal', 'observations']);
        });
        Schema::dropIfExists('smart_stacks');
    }
};
