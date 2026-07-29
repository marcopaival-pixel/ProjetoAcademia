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
        Schema::create('professional_certifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professional_profile_id');
            $table->foreign('professional_profile_id', 'fk_prof_cert_prof_id')
                  ->references('id')->on('professional_profiles')
                  ->onDelete('cascade');
            $table->string('name');
            $table->integer('year')->nullable();
            $table->string('credential_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_certifications');
    }
};
