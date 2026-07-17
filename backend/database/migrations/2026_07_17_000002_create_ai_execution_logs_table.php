<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evolution_report_id')->constrained('evolution_reports')->cascadeOnDelete();
            $table->string('agent', 80)->index();
            $table->string('provider', 40)->nullable();
            $table->string('model', 80)->nullable();
            $table->string('reasoning_effort', 20)->nullable();
            $table->string('prompt_version', 30)->nullable();
            $table->string('schema_version', 30)->nullable();
            $table->string('request_hash', 128)->nullable()->index();
            $table->string('response_id', 128)->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->decimal('cost', 10, 6)->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('status', 30)->index();
            $table->string('error_code', 80)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['evolution_report_id', 'agent']);
            $table->index(['provider', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_execution_logs');
    }
};
