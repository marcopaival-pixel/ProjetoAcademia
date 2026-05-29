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
        Schema::table('plan_features', function (Blueprint $table) {
            if (!Schema::hasColumn('plan_features', 'feature_key')) {
                $table->string('feature_key')->after('plan_id');
            }
            if (!Schema::hasColumn('plan_features', 'is_enabled')) {
                $table->boolean('is_enabled')->default(true)->after('feature_key');
            }
            if (Schema::hasColumn('plan_features', 'feature_name')) {
                $table->dropColumn('feature_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropColumn(['feature_key', 'is_enabled']);
            $table->string('feature_name')->after('plan_id')->nullable();
        });
    }
};
