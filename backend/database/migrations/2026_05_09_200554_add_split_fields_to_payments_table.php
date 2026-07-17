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
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('fee_amount', 10, 2)->default(0.00)->after('amount')->comment('Taxa retida pela plataforma');
            $table->decimal('net_amount', 10, 2)->default(0.00)->after('fee_amount')->comment('Valor líquido após taxas');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'net_amount']);
        });
    }
};
