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
        Schema::table('clinics', function (Blueprint $table) {
            $table->date('sale_date')->nullable();
            $table->string('plan_name')->nullable();
            $table->string('sale_status')->default('ativa'); // ativa, inativa, inadimplente
            $table->string('commission_type')->default('percentual'); // percentual, fixo
            $table->decimal('commission_value', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn(['sale_date', 'plan_name', 'sale_status', 'commission_type', 'commission_value']);
        });
    }
};
