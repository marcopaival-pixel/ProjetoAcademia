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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status', 24)->change();
            $table->string('payment_method')->nullable()->after('status'); // card, pix, boleto
            $table->string('card_brand')->nullable()->after('payment_method');
            $table->char('card_last_four', 4)->nullable()->after('card_brand');
            $table->string('card_expiry')->nullable()->after('card_last_four');
            $table->date('next_billing_date')->nullable()->after('card_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'card_brand', 'card_last_four', 'card_expiry', 'next_billing_date']);
        });
    }
};
