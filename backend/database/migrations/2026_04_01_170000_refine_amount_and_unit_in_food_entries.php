<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_entries', function (Blueprint $table) {
            // Mudando amount para float para cálculos e adicionando unit
            $table->float('amount')->nullable()->change();
            $table->string('unit', 20)->default('g')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('food_entries', function (Blueprint $table) {
            $table->string('amount')->nullable()->change();
            $table->dropColumn('unit');
        });
    }
};
