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
            $table->unsignedInteger('retry_count')->default(0)->after('next_billing_date');
            $table->timestamp('last_attempt_at')->nullable()->after('retry_count');
            $table->unsignedBigInteger('pending_plan_id')->nullable()->after('last_attempt_at');
            $table->timestamp('cancelled_at')->nullable()->after('pending_plan_id');
            $table->timestamp('refunded_at')->nullable()->after('cancelled_at');
            $table->decimal('refunded_amount', 12, 2)->nullable()->after('refunded_at');
            $table->string('reason_for_suspension')->nullable()->after('refunded_amount');
            
            $table->foreign('pending_plan_id')->references('id')->on('plans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['pending_plan_id']);
            $table->dropColumn([
                'retry_count', 'last_attempt_at', 'pending_plan_id', 
                'cancelled_at', 'refunded_at', 'refunded_amount', 'reason_for_suspension'
            ]);
        });
    }
};
