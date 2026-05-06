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
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'tipo')) {
                $table->string('tipo')->default('SUPORTE')->after('user_two_id');
            }
            if (!Schema::hasColumn('conversations', 'status')) {
                $table->string('status')->default('ABERTO')->after('tipo');
            }
            
            // Permitir user_two_id ser nulo se quisermos tickets sem admin atribuído inicialmente,
            // mas o sistema atual exige. Vamos manter a obrigatoriedade por agora para não quebrar o Eloquent,
            // ou alterar para nullable se for melhor para o fluxo de "fila".
            $table->unsignedInteger('user_two_id')->nullable()->change();
        });
        
        // Se a tabela de mensagens não tiver os campos com os nomes pedidos, 
        // vamos manter os originais mas garantir que o conteúdo está lá.
        // O usuário pediu: remetente, mensagem. Atualmente: sender_id, content.
        // Vou manter sender_id e content para não quebrar o código existente (NÃO quebrar funcionalidades).
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'status']);
            $table->unsignedInteger('user_two_id')->nullable(false)->change();
        });
    }
};
