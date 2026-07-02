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
        Schema::create('health_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->string('data_type');
            $table->enum('access_level', ['none', 'view', 'edit'])->default('none');
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['patient_id', 'professional_id', 'data_type'], 'health_perms_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_permissions');
    }
};

