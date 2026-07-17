<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_reactions', function (Blueprint $table) {
            $table->string('visitor_key', 64)->nullable()->after('user_id')->index();
        });

        DB::statement('ALTER TABLE community_reactions MODIFY user_id INT UNSIGNED NULL');

        Schema::table('community_reactions', function (Blueprint $table) {
            $table->unique(['reactable_type', 'reactable_id', 'visitor_key', 'emoji'], 'reaction_visitor_unique');
        });
    }

    public function down(): void
    {
        Schema::table('community_reactions', function (Blueprint $table) {
            $table->dropUnique('reaction_visitor_unique');
            $table->dropColumn('visitor_key');
        });

        DB::statement('ALTER TABLE community_reactions MODIFY user_id INT UNSIGNED NOT NULL');
    }
};
