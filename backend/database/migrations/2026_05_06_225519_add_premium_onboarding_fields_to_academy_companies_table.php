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
            $table->string('account_type')->nullable()->after('tax_id');
            $table->string('state_registration')->nullable()->after('account_type');
            $table->string('municipal_registration')->nullable()->after('state_registration');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('website')->nullable()->after('whatsapp');
            $table->string('instagram')->nullable()->after('website');
            $table->string('street')->nullable()->after('address');
            $table->string('number')->nullable()->after('street');
            $table->string('country')->nullable()->default('Brasil')->after('state');
            $table->string('language', 10)->nullable()->default('pt-BR')->after('country');
            $table->string('currency', 10)->nullable()->default('BRL')->after('language');
            $table->string('timezone')->nullable()->default('America/Sao_Paulo')->after('currency');
            $table->json('onboarding_state')->nullable()->after('onboarding_status');
        });
    }

    public function down(): void
    {
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->dropColumn([
                'account_type',
                'state_registration',
                'municipal_registration',
                'whatsapp',
                'website',
                'instagram',
                'street',
                'number',
                'country',
                'language',
                'currency',
                'timezone',
                'onboarding_state',
            ]);
        });
    }
};
