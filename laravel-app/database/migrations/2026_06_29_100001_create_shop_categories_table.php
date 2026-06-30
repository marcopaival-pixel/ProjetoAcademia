<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_company_id')->index();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image_path')->nullable();
            $table->enum('product_type', ['physical', 'digital', 'service', 'all'])->default('all');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('academy_company_id')->references('id')->on('academy_companies')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('shop_categories')->onDelete('set null');
            $table->unique(['academy_company_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_categories');
    }
};
