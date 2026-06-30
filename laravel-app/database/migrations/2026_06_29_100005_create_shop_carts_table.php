<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->unsignedBigInteger('coupon_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('shop_coupons')->onDelete('set null');
            $table->unique(['user_id', 'academy_company_id']); // um carrinho ativo por aluno/empresa
        });

        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); // preço no momento da adição
            $table->timestamps();

            $table->foreign('cart_id')->references('id')->on('shop_carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_cart_items');
        Schema::dropIfExists('shop_carts');
    }
};
