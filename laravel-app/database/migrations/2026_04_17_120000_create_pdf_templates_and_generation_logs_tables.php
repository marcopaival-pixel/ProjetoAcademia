<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_type', 64)->index();
            $table->text('description')->nullable();
            $table->longText('html_body');
            $table->longText('css_extra')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 32)->default('#1e293b');
            $table->string('secondary_color', 32)->nullable();
            $table->string('accent_color', 32)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pdf_generation_logs', function (Blueprint $table) {
            $table->id();
            // users.id é unsignedInteger neste projeto (migração inicial increments)
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('pdf_template_id')->nullable()->constrained('pdf_templates')->nullOnDelete();
            $table->string('document_type', 64)->index();
            $table->string('template_name')->nullable();
            $table->string('action', 32)->default('download');
            $table->string('filename');
            $table->string('status', 16)->default('success');
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_generation_logs');
        Schema::dropIfExists('pdf_templates');
    }
};
