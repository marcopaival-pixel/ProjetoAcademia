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
        Schema::create('patient_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->string('module_key');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('auto_discovered')->default(true);
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['patient_id', 'module_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_modules');
    }
};

