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
        if (!Schema::hasColumn('patient_access_tokens', 'type')) {
            Schema::table('patient_access_tokens', function (Blueprint $table) {
                $table->string('type')->after('patient_id')->default('access'); // access, activation
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_access_tokens', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
