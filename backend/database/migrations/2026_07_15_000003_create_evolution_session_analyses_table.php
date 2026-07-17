<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evolution_session_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('session_date');
            $table->string('photo_hash', 64);
            $table->json('analysis');
            $table->string('model_name')->nullable();
            $table->unsignedInteger('total_tokens')->default(0);
            $table->decimal('cost_usd', 10, 6)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'session_date', 'photo_hash'], 'evolution_session_analysis_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evolution_session_analyses');
    }
};
