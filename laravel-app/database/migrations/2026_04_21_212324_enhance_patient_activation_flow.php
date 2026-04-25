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
                $table->string('type')->default('access')->after('patient_id'); // access, activation
            });
        }

        if (!Schema::hasColumn('users', 'activated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('activated_at')->nullable()->after('status');
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

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activated_at');
        });
    }
};
