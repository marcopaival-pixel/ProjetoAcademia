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
        Schema::table('role_menu_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('role_menu_permissions', 'created_at')) {
                $table->index('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_menu_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('role_menu_permissions', 'created_at')) {
                $table->dropIndex(['created_at']);
            }
        });
    }
};
