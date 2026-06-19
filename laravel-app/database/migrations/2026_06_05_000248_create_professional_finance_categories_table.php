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
        Schema::create('professional_finance_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('professional_id');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['revenue', 'expense']);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_finance_categories');
    }
};
