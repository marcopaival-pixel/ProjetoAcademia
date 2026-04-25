<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Tabela Principal de Alimentos
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('brand')->nullable()->index();
            $table->string('barcode', 20)->unique()->nullable()->index();
            $table->decimal('base_amount', 10, 2)->default(100.00); // 100g ou 100ml
            $table->enum('unit', ['g', 'ml', 'unit'])->default('g');
            $table->string('data_source')->default('local'); // local, openfoodfacts, etc
            $table->timestamps();
        });

        // 2. Catálogo de Nutrientes (Referência)
        Schema::create('nutrients', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // energy_kcal, protein_g, sodium_mg
            $table->string('name');           // Proteínas, Sódio
            $table->string('unit');           // g, mg, kcal, mcg
            $table->boolean('is_main')->default(false); // Destaque no Dashboard
            $table->timestamps();
        });

        // 3. Tabela Pivot (Valores Específicos por Alimento)
        Schema::create('food_nutrient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_id')->constrained('foods')->cascadeOnDelete();
            $table->foreignId('nutrient_id')->constrained('nutrients');
            $table->decimal('amount', 12, 4); // Valor presente no base_amount
            $table->unique(['food_id', 'nutrient_id']);
        });

        // 4. Log de Integração de APIs
        Schema::create('api_integration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_name');
            $table->string('endpoint');
            $table->integer('status_code');
            $table->integer('response_time_ms')->nullable();
            $table->text('request_payload')->nullable();
            $table->longText('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('food_nutrient');
        Schema::dropIfExists('nutrients');
        Schema::dropIfExists('foods');
        Schema::dropIfExists('api_integration_logs');
    }
};
