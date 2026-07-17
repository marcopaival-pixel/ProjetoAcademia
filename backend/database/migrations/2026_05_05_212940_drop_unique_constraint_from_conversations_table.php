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
            // Criar um índice comum para a FK não reclamar
            $table->index('user_one_id');
            // Agora sim podemos remover o único
            $table->dropUnique('conversations_user_one_id_user_two_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->unique(['user_one_id', 'user_two_id']);
        });
    }
};
