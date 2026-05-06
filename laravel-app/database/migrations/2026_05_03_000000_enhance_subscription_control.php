<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Garantir que a tabela subscriptions tem os campos necessários e status string
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'status')) {
                $table->string('status', 32)->default('PENDENTE')->change();
            } else {
                $table->string('status', 32)->default('PENDENTE')->after('plan_id');
            }
            
            if (!Schema::hasColumn('subscriptions', 'gateway_id')) {
                $table->string('gateway_id')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('subscriptions', 'gateway_type')) {
                $table->string('gateway_type')->nullable()->after('gateway_id');
            }
        });

        // 2. Criar tabela de pagamentos para log e histórico real
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('gateway')->default('mercadopago');
            $table->string('gateway_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('BRL');
            $table->string('status', 32)->default('PENDENTE');
            $table->json('payload')->nullable(); // Guardar resposta completa do gateway
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['gateway_id', 'gateway_type']);
        });
    }
};
