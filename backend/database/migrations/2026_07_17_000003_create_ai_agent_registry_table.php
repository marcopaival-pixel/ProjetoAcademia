<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_agent_registry', function (Blueprint $table) {
            $table->id();
            $table->string('agent_key', 80)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->json('allowed_functions')->nullable();
            $table->json('forbidden_functions')->nullable();
            $table->string('provider', 40)->default('openai');
            $table->string('model', 80)->nullable();
            $table->string('reasoning_effort', 20)->nullable();
            $table->string('prompt_version', 30)->nullable();
            $table->string('schema_version', 30)->nullable();
            $table->boolean('requires_audit')->default(false);
            $table->string('auditor_agent_key', 80)->nullable();
            $table->string('fallback_agent_key', 80)->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_agent_registry');
    }
};
