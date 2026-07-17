<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracao_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->unique()->constrained('academy_companies')->cascadeOnDelete();
            $table->string('nome_provedor', 120);
            $table->string('tipo_envio', 16)->default('smtp');
            $table->string('preset', 32)->default('custom');
            $table->string('smtp_host', 255)->nullable();
            $table->unsignedSmallInteger('smtp_porta')->default(587);
            $table->string('smtp_usuario', 255)->nullable();
            $table->text('smtp_senha')->nullable();
            $table->string('criptografia', 16)->default('tls');
            $table->string('email_remetente', 255)->nullable();
            $table->string('nome_remetente', 255)->nullable();
            $table->unsignedSmallInteger('timeout')->default(30);
            $table->unsignedInteger('limite_envio_por_hora')->default(100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracao_email');
    }
};
