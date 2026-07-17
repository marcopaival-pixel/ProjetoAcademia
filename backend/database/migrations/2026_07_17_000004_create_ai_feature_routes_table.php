<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_feature_routes', function (Blueprint $table) {
            $table->id();
            $table->string('feature_key', 100)->unique();
            $table->string('entry_agent_key', 80);
            $table->string('orchestrator', 160)->nullable();
            $table->boolean('requires_validation')->default(false);
            $table->boolean('requires_audit')->default(false);
            $table->string('auditor_agent_key', 80)->nullable();
            $table->string('failure_behavior', 80)->default('fail_closed');
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_feature_routes');
    }
};
