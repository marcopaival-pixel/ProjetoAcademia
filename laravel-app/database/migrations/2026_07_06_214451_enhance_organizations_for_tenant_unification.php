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
        Schema::table('organizations', function (Blueprint $table) {
            // Self-referencing para sub-tenants (ex: Clínica pertence a uma Empresa)
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('organizations')->onDelete('cascade');
            
            // Campos unificados de AcademyCompany e Clinic
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('logo_path')->nullable()->after('slug');
            $table->string('primary_color')->nullable()->after('logo_path');
            $table->string('custom_domain')->nullable()->after('primary_color');
            $table->json('settings')->nullable()->after('custom_domain');
            
            // Vamos mudar a constraint do enum para string simples para maior flexibilidade (evitar conflitos de enum)
        });
        
        // Modificar a coluna 'type' se o banco suportar (MySQL/Postgres)
        // Como o Laravel Doctrine/DBAL às vezes tem problemas com enum, deixamos como string no nível da aplicação,
        // mas aqui vamos garantir que cabe 'ACADEMIA' e 'CLUBE'.
        DB::statement("ALTER TABLE organizations MODIFY COLUMN type VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id', 'slug', 'logo_path', 'primary_color', 'custom_domain', 'settings'
            ]);
        });
    }
};
