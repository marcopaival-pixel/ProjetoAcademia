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
        Schema::table('commissions', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable()->after('representative_id');
            $table->string('commission_type')->default('percentual')->after('base_amount');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('commission_amount');
            $table->decimal('pending_amount', 12, 2)->default(0)->after('paid_amount');
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn(['clinic_id', 'commission_type', 'paid_amount', 'pending_amount']);
        });
    }
};
