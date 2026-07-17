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
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_corporate')->default(false)->after('type');
            $table->decimal('price_per_professional', 10, 2)->nullable()->after('price');
            $table->integer('min_professionals')->default(1)->after('price_per_professional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['is_corporate', 'price_per_professional', 'min_professionals']);
        });
    }
};
