<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('security_incidents');
        
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('version')->default('1.0');
            $table->string('consent_type', 50); // privacy_policy, terms_of_use, cookies
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('reporter_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->timestamps();
            
            $table->foreign('reporter_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('security_incidents');
    }
};
