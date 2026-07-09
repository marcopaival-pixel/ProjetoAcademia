<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->unique()->constrained('payments')->restrictOnDelete();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->string('provider')->nullable();
            $table->string('provider_invoice_id')->nullable()->index();
            $table->string('invoice_number')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('official_url')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('xml_url')->nullable();
            $table->decimal('gross_amount', 12, 2)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->nullable();
            $table->decimal('iss_rate', 7, 4)->nullable();
            $table->decimal('iss_amount', 12, 2)->nullable();
            $table->string('service_code')->nullable();
            $table->text('service_description')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->nullOnDelete();
            $table->index(['status', 'created_at']);
        });

        Schema::create('fiscal_invoice_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_invoice_id')->nullable()->constrained('fiscal_invoices')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('event', 64);
            $table->string('status_before', 32)->nullable();
            $table->string('status_after', 32)->nullable();
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['payment_id', 'event']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_invoice_logs');
        Schema::dropIfExists('fiscal_invoices');
    }
};
