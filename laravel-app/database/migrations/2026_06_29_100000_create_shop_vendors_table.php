<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('slug');
            $table->unique(['academy_company_id', 'slug']);
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('document', 30)->nullable(); // CNPJ/CPF
            $table->decimal('commission_rate', 5, 2)->default(0); // % retida pela academia
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('active');
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('bank_data')->nullable(); // dados bancários para repasse
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_vendors');
    }
};
