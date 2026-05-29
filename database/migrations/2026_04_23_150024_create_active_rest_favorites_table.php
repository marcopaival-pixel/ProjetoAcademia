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
        Schema::create('active_rest_favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id'); // Combinar com int(10) do users table
            $table->unsignedBigInteger('active_rest_routine_id'); // bigint(20) do routines table
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('active_rest_routine_id')->references('id')->on('active_rest_routines')->onDelete('cascade');
            $table->unique(['user_id', 'active_rest_routine_id'], 'fav_user_routine_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_rest_favorites');
    }
};
