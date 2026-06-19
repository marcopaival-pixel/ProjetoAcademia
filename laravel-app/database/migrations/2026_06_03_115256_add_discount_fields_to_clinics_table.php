<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->string('representative_code_used')->nullable()->after('representative_id')->comment('Código usado na contratação');
            $table->decimal('applied_discount_rate', 5, 2)->default(0.00)->after('representative_code_used')->comment('Percentual de desconto aplicado');
        });
    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn(['representative_code_used', 'applied_discount_rate']);
        });
    }
};
