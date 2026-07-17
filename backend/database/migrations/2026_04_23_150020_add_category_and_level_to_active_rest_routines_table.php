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
        Schema::table('active_rest_routines', function (Blueprint $table) {
            $table->string('category')->default('Mobilidade')->after('title');
            $table->string('recommended_level')->default('Iniciante')->after('intensity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('active_rest_routines', function (Blueprint $table) {
            //
        });
    }
};
