<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_envio_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('academy_companies')->nullOnDelete();
            $table->unsignedInteger('usuario_id')->nullable()->index();
            $table->string('email_destino', 255);
            $table->string('assunto', 500)->nullable();
            $table->text('mensagem')->nullable();
            $table->string('status', 16);
            $table->text('erro')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('data_envio')->useCurrent();
            $table->timestamps();

            $table->index(['empresa_id', 'data_envio']);
            $table->index('status');

            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_envio_email');
    }
};
