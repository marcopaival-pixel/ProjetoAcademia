<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code', 32)->unique();
            $table->string('state', 40)->default('RECEIVED')->index();
            $table->string('severity', 16)->default('medium');
            $table->string('module', 64)->nullable();
            $table->unsignedBigInteger('system_error_id')->nullable()->index();
            $table->unsignedBigInteger('affected_user_id')->nullable()->index();
            $table->string('affected_role', 32)->nullable();
            $table->string('route_path')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->text('user_message')->nullable();
            $table->text('error_summary')->nullable();
            $table->longText('stack_trace')->nullable();
            $table->json('evidence')->nullable();
            $table->json('impact_analysis')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('line_start')->nullable();
            $table->unsignedInteger('line_end')->nullable();
            $table->longText('code_snippet')->nullable();
            $table->longText('proposed_diff')->nullable();
            $table->string('approved_change_hash', 64)->nullable();
            $table->json('authorization')->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->string('risk_level', 16)->nullable();
            $table->boolean('approval_patch')->default(false);
            $table->boolean('approval_staging')->default(false);
            $table->boolean('approval_production')->default(false);
            $table->string('commit_hash', 40)->nullable();
            $table->string('branch_name')->nullable();
            $table->string('rollback_command')->nullable();
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_incidents');
    }
};
