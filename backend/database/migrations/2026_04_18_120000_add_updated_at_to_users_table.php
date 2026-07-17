<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona a coluna updated_at à tabela users.
 *
 * A migration original (0001_01_01_000000) criou apenas created_at,
 * e o modelo User havia sido configurado com UPDATED_AT = null para contornar
 * a ausência desta coluna. Esta migration restaura a auditabilidade completa.
 *
 * Impacto: Baixo — coluna nullable, todos os registros existentes ficam com null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
