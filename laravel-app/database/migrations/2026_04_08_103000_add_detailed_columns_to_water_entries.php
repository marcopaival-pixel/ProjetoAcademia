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
        Schema::table('water_entries', function (Blueprint $table) {
            $table->timestamp('drank_at')->nullable()->after('entry_date');
            $table->string('source', 20)->default('manual')->after('amount_ml');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_entries', function (Blueprint $table) {
            $table->dropColumn(['drank_at', 'source']);
        });
    }
};
