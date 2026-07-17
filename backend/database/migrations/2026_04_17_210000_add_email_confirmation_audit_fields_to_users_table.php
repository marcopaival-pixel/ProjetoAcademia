<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos de auditoria do fluxo de confirmação de e-mail (auto cadastro).
     * Nota: estado "confirmado" continua em email_verified_at; token em email_verification_token.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('data_envio_confirmacao')->nullable()->after('email_verification_expires_at');
            $table->unsignedInteger('tentativas_envio')->default(0)->after('data_envio_confirmacao');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['data_envio_confirmacao', 'tentativas_envio']);
        });
    }
};
