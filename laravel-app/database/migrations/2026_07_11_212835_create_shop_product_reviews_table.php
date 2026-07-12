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
        Schema::create('shop_product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('shop_products')->cascadeOnDelete();
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('rating'); // 1 a 5
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true); // padrão aprovado para facilidade em dev
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_reviews');
    }
};
