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
        Schema::create('clinic_protocols', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id');
            $table->unsignedBigInteger('especialidade_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('objective')->nullable();
            $table->string('protocol')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
            $table->foreign('especialidade_id')->references('id')->on('especialidades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_protocols');
    }
};
