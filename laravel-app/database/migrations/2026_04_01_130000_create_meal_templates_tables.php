<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('name', 120);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'name']);
        });

        Schema::create('meal_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_template_id')->constrained('meal_templates')->cascadeOnDelete();
            $table->string('meal_type', 32)->default('other');
            $table->string('food_name', 200);
            $table->unsignedSmallInteger('calories')->default(0);
            $table->decimal('protein_g', 6, 2)->default(0);
            $table->decimal('carbs_g', 6, 2)->default(0);
            $table->decimal('fat_g', 6, 2)->default(0);
            $table->unsignedSmallInteger('position')->default(0);
            $table->index(['meal_template_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_template_items');
        Schema::dropIfExists('meal_templates');
    }
};
