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
            $table->integer('health_score')->nullable()->comment('0-100 score of company health');
            $table->string('churn_risk_level')->nullable()->comment('Baixo, Médio, Alto');
            $table->json('health_reasons')->nullable();
            $table->timestamp('last_health_calculated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->dropColumn([
                'health_score',
                'churn_risk_level',
                'health_reasons',
                'last_health_calculated_at',
            ]);
        });
    }
};
