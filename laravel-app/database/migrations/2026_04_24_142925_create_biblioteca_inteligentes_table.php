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
        Schema::create('biblioteca_inteligente', function (Blueprint $table) {
            $table->id();
            $table->string('modulo')->nullable();
            $table->string('categoria')->nullable();
            $table->string('tipo_item'); // LISTA, ITEM_LISTA, RESPOSTA, TEMPLATE, PROTOCOLO, STACK, EXERCICIO, ALIMENTO, SUPLEMENTO, MEDICAMENTO
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->text('pergunta')->nullable();
            $table->text('palavras_chave')->nullable();
            $table->longText('conteudo')->nullable();
            $table->string('origem')->default('IA');
            $table->string('visibilidade')->default('PUBLICO');
            $table->string('status')->default('ATIVO');
            $table->string('versao')->nullable();
            $table->integer('uso_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblioteca_inteligente');
    }
};
