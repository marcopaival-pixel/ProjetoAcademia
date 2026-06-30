<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Atualiza FK de shop_coupon_usages agora que shop_orders existe
        Schema::table('shop_coupon_usages', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('shop_orders')->onDelete('cascade');
        });

        Schema::create('shop_wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->timestamp('notified_at')->nullable(); // notificado quando voltou ao estoque
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('cascade');
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('shop_points_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->unsignedInteger('balance_points')->default(0);
            $table->decimal('balance_cashback', 10, 2)->default(0);
            $table->unsignedInteger('lifetime_points_earned')->default(0);
            $table->decimal('lifetime_cashback_earned', 10, 2)->default(0);
            $table->enum('tier', ['bronze', 'silver', 'gold', 'diamond'])->default('bronze');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
        });

        Schema::create('shop_points_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->enum('type', ['earn', 'redeem', 'expire', 'bonus', 'refund']);
            $table->integer('points'); // positivo = entrada, negativo = saída
            $table->decimal('cashback_amount', 10, 2)->nullable();
            $table->string('description');
            $table->string('source')->nullable(); // order, attendance, referral
            $table->unsignedBigInteger('source_id')->nullable(); // ID da entidade de origem
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('shop_points_wallets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('shop_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->json('product_ids'); // IDs recomendados em ordem de relevância
            $table->string('reason')->nullable(); // goal_match, purchase_history, frequency
            $table->json('context')->nullable();  // dados usados para gerar a recomendação
            $table->decimal('score', 5, 4)->nullable();
            $table->timestamp('expires_at'); // TTL do cache (24h)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_recommendations');
        Schema::dropIfExists('shop_points_transactions');
        Schema::dropIfExists('shop_points_wallets');
        Schema::dropIfExists('shop_wishlists');

        Schema::table('shop_coupon_usages', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
};
