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
        Schema::table('exercises_catalog', function (Blueprint $table) {
            $table->json('tips')->nullable()->after('instructions');
            $table->json('common_mistakes')->nullable()->after('tips');
            $table->string('video_type', 20)->default('youtube')->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises_catalog', function (Blueprint $table) {
            $table->dropColumn(['tips', 'common_mistakes', 'video_type']);
        });
    }
};
