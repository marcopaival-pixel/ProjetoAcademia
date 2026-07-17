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
            $table->boolean('is_demo')->default(false)->after('is_premium');
            $table->dateTime('demo_expires_at')->nullable()->after('is_demo');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedInteger('converted_user_id')->nullable()->after('responsavel_id');
            $table->foreign('converted_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['converted_user_id']);
            $table->dropColumn('converted_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_demo', 'demo_expires_at']);
        });
    }
};
