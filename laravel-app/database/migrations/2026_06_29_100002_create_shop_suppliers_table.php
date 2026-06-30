<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->string('name');
            $table->string('document', 30)->nullable(); // CNPJ
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->json('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_suppliers');
    }
};
