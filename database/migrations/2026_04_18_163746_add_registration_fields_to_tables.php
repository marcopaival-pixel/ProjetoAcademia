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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 25)->nullable()->after('email');
            }
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('professional_profiles', 'company_name')) {
                $table->string('company_name', 120)->nullable()->after('specialty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn('company_name');
        });
    }
};
