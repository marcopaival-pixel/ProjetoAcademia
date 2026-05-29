<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->string('action');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('origin')->default('system'); // system, admin, webhook
            $table->string('ip_address', 45)->nullable();
            $table->text('observation')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->nullOnDelete();
            
            $table->index(['user_id', 'academy_company_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_logs');
    }
};
