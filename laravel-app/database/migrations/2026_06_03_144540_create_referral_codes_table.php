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
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            
            $table->unsignedInteger('representative_id');
            $table->foreign('representative_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('commercial_proposal_id')->nullable();
            $table->foreign('commercial_proposal_id')->references('id')->on('commercial_proposals')->onDelete('set null');
            
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('set null');
            
            $table->string('status')->default('DISPONIVEL'); // DISPONIVEL, RESERVADO, UTILIZADO, EXPIRADO, CANCELADO
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};
