<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('coupon_id')->nullable()->index();
            $table->string('order_number')->unique(); // SHP-2026-00001

            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'refunded',
            ])->default('pending')->index();

            // Valores
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Fidelidade
            $table->unsignedInteger('points_earned')->default(0);
            $table->decimal('cashback_amount', 10, 2)->default(0);

            // Pagamento
            $table->string('payment_method')->nullable(); // pix, credit_card, points
            $table->string('payment_gateway')->nullable(); // mercadopago, asaas
            $table->string('gateway_payment_id')->nullable()->index();
            $table->string('gateway_status')->nullable();

            // Entrega
            $table->string('shipping_method')->nullable(); // correios, transportadora, pickup
            $table->json('shipping_address')->nullable();
            $table->string('tracking_code')->nullable();
            $table->timestamp('pickup_at')->nullable(); // retirada na academia

            // Datas de ciclo de vida
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('coupon_id')->references('id')->on('shop_coupons')->onDelete('set null');
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('vendor_id')->index();

            // Snapshot do produto no momento da compra
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->enum('product_type', ['physical', 'digital', 'service']);

            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Comissão do marketplace
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->enum('commission_status', ['pending', 'released', 'paid'])->nullable();

            // Download (digital)
            $table->string('download_token')->nullable()->unique();
            $table->timestamp('download_expires_at')->nullable();
            $table->unsignedInteger('download_count')->default(0);

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('shop_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('restrict');
            $table->foreign('vendor_id')->references('id')->on('shop_vendors')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
    }
};
