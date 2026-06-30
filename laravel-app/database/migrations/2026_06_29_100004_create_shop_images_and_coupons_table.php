<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('path');
            $table->string('alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('cascade');
        });

        Schema::create('shop_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->unsignedInteger('created_by')->index();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->enum('type', ['percentage', 'fixed', 'free_shipping', 'product_gift']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('minimum_order_value', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();     // teto do desconto (para %)
            $table->enum('applies_to', ['all', 'categories', 'products'])->default('all');
            $table->json('category_ids')->nullable();
            $table->json('product_ids')->nullable();
            $table->boolean('free_shipping')->default(false);
            $table->unsignedInteger('max_uses_total')->nullable();
            $table->unsignedInteger('max_uses_per_user')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_single_use')->default(false);
            $table->string('campaign')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'paused', 'expired'])->default('active');
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::create('shop_coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->decimal('discount_applied', 10, 2);
            $table->timestamps();

            $table->foreign('coupon_id')->references('id')->on('shop_coupons')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_coupon_usages');
        Schema::dropIfExists('shop_coupons');
        Schema::dropIfExists('shop_product_images');
    }
};
