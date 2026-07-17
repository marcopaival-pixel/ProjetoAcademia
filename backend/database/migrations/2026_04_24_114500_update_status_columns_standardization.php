<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Atualizar status na tabela de assinaturas
        // Nota: Em MySQL/MariaDB, mudar enum requer recriar ou usar string. 
        // Para maior flexibilidade, vamos mudar para string se já não for.
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status', 32)->default('active')->change();
        });

        // Atualizar status na tabela de usuários
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 32)->default('active')->change();
        });
        
        // Adicionar campos extras na tabela de assinaturas para controle fino se necessário
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'days_overdue')) {
                $table->integer('days_overdue')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        // Reverter não é estritamente necessário para string, mas se quiser voltar para enum:
        // Schema::table('subscriptions', function (Blueprint $table) {
        //     $table->enum('status', ['active', 'expired', 'cancelled'])->default('active')->change();
        // });
    }
};
