<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('api_access_logs')) {
            Schema::create('api_access_logs', function (Blueprint $table) {
                $table->id();
                $table->uuid('request_id')->nullable()->index();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('token_id')->nullable()->index();
                $table->string('method', 10);
                $table->string('path', 500);
                $table->unsignedSmallInteger('status_code')->nullable();
                $table->unsignedInteger('duration_ms')->nullable();
                $table->string('ip', 45)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->timestamps();

                $table->index(['created_at', 'path']);
            });
        }

        if (! Schema::hasTable('auth_audit_logs')) {
            Schema::create('auth_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->string('email')->nullable()->index();
                $table->string('event', 50)->index();
                $table->string('guard', 30)->default('web');
                $table->boolean('success')->default(true);
                $table->string('ip', 45)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }

        if (! Schema::hasTable('client_error_logs')) {
            Schema::create('client_error_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->string('type', 50)->default('error');
                $table->text('message');
                $table->text('stack')->nullable();
                $table->string('url', 500)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->string('ip', 45)->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_error_logs');
        Schema::dropIfExists('auth_audit_logs');
        Schema::dropIfExists('api_access_logs');
    }
};
