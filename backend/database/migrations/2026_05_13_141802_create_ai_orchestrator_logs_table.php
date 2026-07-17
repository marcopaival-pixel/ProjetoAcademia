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
        Schema::create('ai_orchestrator_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('clinic_id')->index()->nullable();
            $table->string('agent_type')->index();
            $table->text('user_message');
            $table->longText('ai_response')->nullable();
            $table->integer('tokens_used')->default(0);
            $table->integer('response_time_ms')->default(0);
            $table->string('status')->default('success'); // success, error, limit_reached
            $table->json('context')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_orchestrator_logs');
    }
};
