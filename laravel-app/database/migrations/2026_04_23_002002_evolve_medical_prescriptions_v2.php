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
        Schema::table('medical_prescriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('especialidade_id')->nullable()->after('professional_id');
            $table->unsignedBigInteger('academy_company_id')->nullable()->after('especialidade_id');
            $table->string('objective')->nullable()->after('date');
            $table->string('protocol')->nullable()->after('objective');
            
            $table->foreign('especialidade_id')->references('id')->on('especialidades')->onDelete('set null');
            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_prescriptions', function (Blueprint $table) {
            $table->dropForeign(['academy_company_id']);
            $table->dropForeign(['especialidade_id']);
            $table->dropColumn(['especialidade_id', 'academy_company_id', 'objective', 'protocol']);
        });
    }
};
