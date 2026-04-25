<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('communication_groups', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('allow_self_join');
            $table->boolean('can_members_send_messages')->default(true)->after('is_active');
        });

        // Modificar o enum de role na tabela pivot. 
        // Como o SQLite/MySQL lidam com enums de forma diferente, a forma mais compatível é recrear a coluna ou usar DB::statement se for MySQL.
        // Assumindo MySQL conforme pedido.
        DB::statement("ALTER TABLE communication_group_user MODIFY COLUMN role ENUM('member', 'moderator', 'admin') DEFAULT 'member'");
    }

    public function down(): void
    {
        Schema::table('communication_groups', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'can_members_send_messages']);
        });

        DB::statement("ALTER TABLE communication_group_user MODIFY COLUMN role ENUM('member', 'admin') DEFAULT 'member'");
    }
};
