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
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->string('mercadopago_user_id')->nullable()->after('tax_id')->comment('ID da conta do vendedor no Mercado Pago');
            $table->decimal('platform_fee_percent', 5, 2)->default(10.00)->after('mercadopago_user_id')->comment('Porcentagem retida pela plataforma');
            $table->decimal('platform_fee_fixed', 10, 2)->default(0.00)->after('platform_fee_percent')->comment('Taxa fixa retida pela plataforma');
        });
    }

    public function down(): void
    {
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->dropColumn(['mercadopago_user_id', 'platform_fee_percent', 'platform_fee_fixed']);
        });
    }
};
