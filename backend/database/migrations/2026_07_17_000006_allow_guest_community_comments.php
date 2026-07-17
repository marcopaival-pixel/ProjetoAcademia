<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_comments', function (Blueprint $table) {
            $table->string('visitor_key', 64)->nullable()->after('user_id')->index();
            $table->string('visitor_name', 80)->nullable()->after('visitor_key');
        });

        DB::statement('ALTER TABLE community_comments MODIFY user_id INT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('community_comments', function (Blueprint $table) {
            $table->dropColumn(['visitor_key', 'visitor_name']);
        });

        DB::statement('ALTER TABLE community_comments MODIFY user_id INT UNSIGNED NOT NULL');
    }
};
