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
        Schema::dropIfExists('mensagens');
        Schema::create('internal_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('remetente_id'); // FK -> users
            $table->unsignedInteger('destinatario_id'); // FK -> users
            $table->string('assunto', 200);
            $table->text('mensagem');
            $table->boolean('lida')->default(false);
            $table->timestamp('data_envio')->useCurrent();
            $table->timestamp('data_leitura')->nullable();
            
            $table->foreign('remetente_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('destinatario_id')->references('id')->on('users')->cascadeOnDelete();
            
            $table->index(['destinatario_id', 'lida']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensagens');
    }
};
