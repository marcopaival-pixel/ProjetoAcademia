<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->unsignedBigInteger('vendor_id')->index();       // FK shop_vendors
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('supplier_id')->nullable()->index(); // FK shop_suppliers

            // Tipo do produto — central para o marketplace-ready
            $table->enum('type', ['physical', 'digital', 'service'])->index();

            // Identificação
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('sku', 100)->nullable()->index();

            // Preços
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable(); // uso interno

            // Estoque (físico)
            $table->boolean('manage_stock')->default(false);
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->unsignedInteger('stock_alert_threshold')->nullable();

            // Logística (físico)
            $table->decimal('weight', 8, 3)->nullable(); // kg
            $table->json('dimensions')->nullable();       // {length, width, height} em cm

            // Digital
            $table->string('downloadable_file')->nullable();        // caminho no storage
            $table->unsignedInteger('download_limit')->nullable();  // null = ilimitado
            $table->unsignedInteger('download_expiry_days')->nullable();

            // Serviço
            $table->boolean('requires_scheduling')->default(false);

            // IA
            $table->json('ai_tags')->nullable();       // tags para motor de recomendação
            $table->json('goal_types')->nullable();    // ["emagrecimento","hipertrofia"]

            // Visibilidade
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['draft', 'pending_review', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('shop_vendors')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('shop_categories')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('shop_suppliers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};
