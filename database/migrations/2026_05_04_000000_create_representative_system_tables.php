<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adicionar representative_id aos usuários (quem indicou este usuário)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'representative_id')) {
                $table->unsignedInteger('representative_id')->nullable()->after('id');
                $table->foreign('representative_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Campo para identificar se o próprio usuário é um representante (além da Role)
            if (!Schema::hasColumn('users', 'is_representative')) {
                $table->boolean('is_representative')->default(false)->after('representative_id');
            }
        });

        // 2. Adicionar comissão padrão aos planos
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(0.00)->after('price')->comment('Percentual de comissão (ex: 10.00 para 10%)');
            }
        });

        // 3. Criar tabela de comissões
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('representative_id');
            $table->unsignedInteger('user_id')->comment('Usuário que realizou o pagamento');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('subscription_id')->nullable();
            
            $table->decimal('base_amount', 12, 2)->comment('Valor base do pagamento');
            $table->decimal('commission_rate', 5, 2)->comment('Taxa aplicada no momento');
            $table->decimal('commission_amount', 12, 2)->comment('Valor final da comissão');
            
            $table->string('status', 32)->default('PENDENTE')->comment('PENDENTE, DISPONIVEL, PAGO, CANCELADO');
            $table->timestamp('available_at')->nullable()->comment('Data em que a comissão ficará disponível para saque');
            $table->timestamp('paid_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('representative_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['representative_id']);
            $table->dropColumn(['representative_id', 'is_representative']);
        });
    }
};
