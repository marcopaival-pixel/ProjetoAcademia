<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de pacotes de créditos
        if (!Schema::hasTable('creditos_pacotes')) {
            Schema::create('creditos_pacotes', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->integer('quantidade');
                $table->decimal('valor', 10, 2);
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        // Tabela de compras de créditos
        if (!Schema::hasTable('creditos_compras')) {
            Schema::create('creditos_compras', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->integer('quantidade');
                $table->decimal('valor', 10, 2);
                $table->string('status')->default('PENDENTE'); // PENDENTE, PAGO, CANCELADO
                $table->string('gateway')->nullable();
                $table->string('payment_id')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Adicionar campo creditos em users se não existir
        if (!Schema::hasColumn('users', 'creditos')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('creditos')->default(0)->after('email');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('creditos_compras');
        Schema::dropIfExists('creditos_pacotes');
        
        if (Schema::hasColumn('users', 'creditos')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('creditos');
            });
        }
    }
};
