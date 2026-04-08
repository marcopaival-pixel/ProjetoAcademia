<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Limpar possíveis resíduos de migrações falhadas com tipos incorretos
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');

        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_one_id');
            $table->unsignedInteger('user_two_id');
            $table->timestamps();
            
            $table->foreign('user_one_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_one_id', 'user_two_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('conversation_id');
            $table->unsignedInteger('sender_id');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Ajustando os tipos de FK para Integer (compatível com a tabela users legado/base)
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
