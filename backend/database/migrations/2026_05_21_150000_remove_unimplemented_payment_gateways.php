<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove gateways removidos do código (PaymentGatewayRegistry).
     */
    public function up(): void
    {
        DB::table('payment_settings')
            ->whereIn('gateway', ['stripe', 'pagseguro'])
            ->delete();
    }

    public function down(): void
    {
        // Sem restauração automática — credenciais não devem ser recriadas em vazio.
    }
};
