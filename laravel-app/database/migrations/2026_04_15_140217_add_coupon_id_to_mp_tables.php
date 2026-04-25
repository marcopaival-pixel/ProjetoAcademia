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
        Schema::table('mercadopago_payment_credits', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
        });

        Schema::table('mercadopago_subscriptions', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mercadopago_payment_credits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
        });

        Schema::table('mercadopago_subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
        });
    }
};
