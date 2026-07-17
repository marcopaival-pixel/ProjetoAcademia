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
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->text('client_id')->nullable()->after('gateway');
            $table->text('client_secret')->nullable()->after('client_id');
            $table->string('webhook_url')->nullable()->after('webhook_secret');
            $table->integer('timeout')->default(45)->after('webhook_url');
            $table->integer('priority')->default(1)->after('timeout');
            
            // Business Rules
            $table->decimal('penalty_percent', 5, 2)->default(0.00)->after('status');
            $table->decimal('interest_percent', 5, 2)->default(0.00)->after('penalty_percent');
            $table->decimal('discount_percent', 5, 2)->default(0.00)->after('interest_percent');
            $table->integer('tolerance_days')->default(0)->after('discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn([
                'client_id', 'client_secret', 'webhook_url', 'timeout', 'priority',
                'penalty_percent', 'interest_percent', 'discount_percent', 'tolerance_days'
            ]);
        });
    }
};
