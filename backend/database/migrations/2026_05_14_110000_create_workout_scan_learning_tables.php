<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_scan_failure_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_import_log_id')->nullable()->constrained('workout_import_logs')->nullOnDelete();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('clinic_id')->nullable()->index();
            $table->unsignedBigInteger('academy_company_id')->nullable()->index();
            $table->string('image_path')->nullable();
            $table->string('error_type');
            $table->string('failed_stage');
            $table->json('ai_identified_content')->nullable();
            $table->json('expected_result')->nullable();
            $table->string('model_version')->nullable();
            $table->text('error_message')->nullable();
            $table->string('status')->default('registered')->index();
            $table->timestamps();
        });

        Schema::create('workout_scan_correction_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('failure_case_id')->constrained('workout_scan_failure_cases')->cascadeOnDelete();
            $table->unsignedBigInteger('clinic_id')->nullable()->index();
            $table->unsignedBigInteger('academy_company_id')->nullable()->index();
            $table->string('correction_type');
            $table->json('proposal');
            $table->decimal('confidence', 5, 2)->default(0);
            $table->boolean('requires_human_review')->default(true);
            $table->string('status')->default('proposed')->index();
            $table->text('rationale')->nullable();
            $table->timestamps();
        });

        Schema::create('workout_scan_regression_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_proposal_id')->constrained('workout_scan_correction_proposals')->cascadeOnDelete();
            $table->foreignId('failure_case_id')->nullable()->constrained('workout_scan_failure_cases')->nullOnDelete();
            $table->foreignId('workout_import_log_id')->nullable()->constrained('workout_import_logs')->nullOnDelete();
            $table->unsignedBigInteger('clinic_id')->nullable()->index();
            $table->unsignedBigInteger('academy_company_id')->nullable()->index();
            $table->string('dataset_type');
            $table->string('status')->default('pending')->index();
            $table->json('expected_result')->nullable();
            $table->json('actual_result')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('workout_scan_knowledge_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_proposal_id')->nullable()->constrained('workout_scan_correction_proposals')->nullOnDelete();
            $table->unsignedBigInteger('clinic_id')->nullable()->index();
            $table->unsignedBigInteger('academy_company_id')->nullable()->index();
            $table->string('rule_key')->index();
            $table->unsignedInteger('version')->default(1);
            $table->string('correction_type');
            $table->json('rule_payload');
            $table->text('error_cause')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('reverted_at')->nullable();
            $table->json('history')->nullable();
            $table->timestamps();
            $table->unique(['rule_key', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_scan_knowledge_rules');
        Schema::dropIfExists('workout_scan_regression_tests');
        Schema::dropIfExists('workout_scan_correction_proposals');
        Schema::dropIfExists('workout_scan_failure_cases');
    }
};
