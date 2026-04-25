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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('empresa')->nullable();
            $table->string('origem')->nullable();
            $table->unsignedInteger('responsavel_id')->nullable();
            $table->foreign('responsavel_id')->references('id')->on('users')->onDelete('set null');
            $table->enum('status', ['Novo', 'Em contato', 'Em negociação', 'Convertido', 'Perdido'])->default('Novo');
            $table->text('observacao')->nullable();
            $table->decimal('valor_estimado', 10, 2)->nullable();
            $table->timestamp('previsao_fechamento')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
