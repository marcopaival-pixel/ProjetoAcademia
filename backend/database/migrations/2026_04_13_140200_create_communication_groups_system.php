<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Adicionar departamento ao usuário
        Schema::table('users', function (Blueprint $table) {
            $table->string('department', 50)->nullable()->after('is_admin');
        });

        // 2. Tabela de Grupos de Comunicação
        Schema::create('communication_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_private')->default(true); // Requer aprovação se true
            $table->boolean('allow_self_join')->default(false); // Permite solicitar entrada
            $table->timestamps();
        });

        // 3. Tabela Pivot Grupos <=> Usuários
        Schema::create('communication_group_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('group_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('role', ['member', 'admin'])->default('member');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('communication_groups')->onDelete('cascade');
            $table->unique(['user_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_group_user');
        Schema::dropIfExists('communication_groups');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }
};
