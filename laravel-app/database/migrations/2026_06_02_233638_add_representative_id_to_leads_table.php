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
        if (!Schema::hasColumn('leads', 'representative_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->unsignedInteger('representative_id')->nullable()->after('id');
                $table->foreign('representative_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['representative_id']);
            $table->dropColumn('representative_id');
        });
    }
};
