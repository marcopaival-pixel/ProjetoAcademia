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
        Schema::create('professional_finance_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('professional_id');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('monthly_goal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_finance_goals');
    }
};
