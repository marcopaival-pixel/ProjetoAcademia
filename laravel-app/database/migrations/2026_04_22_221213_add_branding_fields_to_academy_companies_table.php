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
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('slug');
            $table->string('primary_color', 7)->default('#3b82f6')->after('logo_path');
            $table->string('accent_color', 7)->default('#10b981')->after('primary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'primary_color', 'accent_color']);
        });
    }
};
