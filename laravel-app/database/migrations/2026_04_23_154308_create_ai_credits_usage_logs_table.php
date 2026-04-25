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
        Schema::create('ai_credits_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('action_type'); // e.g., 'generate_diet', 'analyze_meal', 'chat'
            $table->integer('credits_consumed');
            $table->json('metadata')->nullable();
            $table->string('response_cache_key')->nullable()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credits_usage_logs');
    }
};
