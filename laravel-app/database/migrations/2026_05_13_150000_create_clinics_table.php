<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id')->nullable(); // Account Owner
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 7)->default('#10b981');
            $table->string('custom_domain')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->nullOnDelete();
        });

        // Adicionar clinic_id às tabelas principais para isolamento
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable()->after('academy_company_id');
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable()->after('id');
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
        });

        Schema::table('body_assessments', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable()->after('academy_company_id');
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
        });

        Schema::table('training_plans', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable()->after('id');
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
        });

        Schema::table('ai_chats', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable()->after('user_id');
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('training_plans', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });

        Schema::table('body_assessments', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });

        Schema::table('ai_chats', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });

        Schema::dropIfExists('clinics');
    }
};
