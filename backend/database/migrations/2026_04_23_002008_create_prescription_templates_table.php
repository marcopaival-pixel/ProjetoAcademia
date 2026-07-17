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
        Schema::create('prescription_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('especialidade_id');
            $table->unsignedInteger('professional_id');
            $table->string('title');
            $table->text('content');
            $table->timestamps();

            $table->foreign('especialidade_id')->references('id')->on('especialidades')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_templates');
    }
};
