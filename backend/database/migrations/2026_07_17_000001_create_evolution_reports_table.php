<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evolution_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->foreignId('consent_id')->nullable()->constrained('user_consents')->nullOnDelete();
            $table->date('current_session_date')->nullable()->index();
            $table->date('previous_session_date')->nullable();
            $table->string('status', 40)->index();
            $table->string('provider', 40)->nullable();
            $table->json('validation_result')->nullable();
            $table->json('objective_metrics')->nullable();
            $table->json('comparison_result')->nullable();
            $table->json('audit_result')->nullable();
            $table->json('final_report')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('limited_reason')->nullable();
            $table->string('prompt_version', 30)->nullable();
            $table->string('schema_version', 30)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'current_session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evolution_reports');
    }
};
