<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway'); // mercadopago, pagseguro, asaas, stripe
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->text('public_key')->nullable();
            $table->text('access_token')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->boolean('enable_credit_card')->default(false);
            $table->boolean('enable_pix')->default(false);
            $table->boolean('enable_boleto')->default(false);
            $table->integer('boleto_expiration_days')->default(3);
            $table->integer('pix_expiration_minutes')->default(30);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
