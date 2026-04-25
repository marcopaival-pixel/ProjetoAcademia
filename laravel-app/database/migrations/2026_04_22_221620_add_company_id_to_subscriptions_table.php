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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->foreignId('academy_company_id')->nullable()->after('user_id')->constrained('academy_companies')->nullOnDelete();
            $table->string('billing_type')->default('individual')->after('academy_company_id'); // individual, corporate
            $table->integer('max_professionals')->nullable()->after('billing_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable(false)->change();
            $table->dropForeign(['academy_company_id']);
            $table->dropColumn(['academy_company_id', 'billing_type', 'max_professionals']);
        });
    }
};
