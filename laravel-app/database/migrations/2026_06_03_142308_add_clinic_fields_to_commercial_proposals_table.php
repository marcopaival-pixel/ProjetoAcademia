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
        Schema::table('commercial_proposals', function (Blueprint $table) {
            $table->string('clinic_name')->nullable();
            $table->string('clinic_cnpj')->nullable();
            $table->string('clinic_city')->nullable();
            $table->string('clinic_state')->nullable();
            $table->string('clinic_phone')->nullable();
            $table->string('clinic_contact')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
            
            // Alterar o status para aceitar novos valores
            $table->string('status')->default('Ativa')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commercial_proposals', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn([
                'clinic_name',
                'clinic_cnpj',
                'clinic_city',
                'clinic_state',
                'clinic_phone',
                'clinic_contact',
                'clinic_id'
            ]);
        });
    }
};
