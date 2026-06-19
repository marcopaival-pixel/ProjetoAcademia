<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deploy_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version', 32);
            $table->string('environment', 32); // homologacao, production
            $table->string('status', 32)->default('pending'); // pending, in_progress, success, failed
            $table->string('homolog_status', 32)->nullable(); // pending, approved, rejected
            $table->string('impact_level', 16)->default('medium'); // low, medium, high
            $table->string('risk_level', 16)->default('low'); // low, medium, high
            $table->unsignedInteger('deployed_by')->nullable();
            $table->foreign('deployed_by')->references('id')->on('users')->nullOnDelete();
            $table->string('git_branch')->nullable();
            $table->string('git_commit', 64)->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_message')->nullable();
            $table->unsignedSmallInteger('files_changed_count')->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->index(['environment', 'status']);
            $table->index(['environment', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deploy_releases');
    }
};
