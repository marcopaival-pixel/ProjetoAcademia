<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->string('portal', 32)->default('app')->after('is_required');
            $table->string('match_mode', 16)->default('exact')->after('route');
            $table->boolean('is_container')->default(false)->after('match_mode');
            $table->foreign('parent_id')->references('id')->on('menus')->nullOnDelete();
        });

        Schema::create('role_menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->boolean('pode_visualizar')->default(false);
            $table->boolean('pode_criar')->default(false);
            $table->boolean('pode_editar')->default(false);
            $table->boolean('pode_excluir')->default(false);
            $table->boolean('pode_exportar')->default(false);
            $table->boolean('pode_imprimir')->default(false);
            $table->foreignId('academy_company_id')->nullable()->constrained('academy_companies')->nullOnDelete();
            $table->timestamps();

            $table->index(['role_id', 'menu_id']);
            $table->index(['academy_company_id']);
        });

        Schema::create('menu_permission_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('academy_company_id')->nullable()->constrained('academy_companies')->nullOnDelete();
            $table->string('action', 64);
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_permission_audit_logs');
        Schema::dropIfExists('role_menu_permissions');

        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'portal', 'match_mode', 'is_container']);
        });
    }
};
