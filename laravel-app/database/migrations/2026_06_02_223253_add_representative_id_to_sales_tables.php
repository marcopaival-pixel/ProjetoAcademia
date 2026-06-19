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
        // Add to clinics
        if (!Schema::hasColumn('clinics', 'representative_id')) {
            Schema::table('clinics', function (Blueprint $table) {
                $table->unsignedInteger('representative_id')->nullable();
                $table->foreign('representative_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // Add to commercial_proposals
        if (Schema::hasTable('commercial_proposals') && !Schema::hasColumn('commercial_proposals', 'representative_id')) {
            Schema::table('commercial_proposals', function (Blueprint $table) {
                $table->unsignedInteger('representative_id')->nullable();
                $table->foreign('representative_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // Add to contracts
        if (Schema::hasTable('contracts') && !Schema::hasColumn('contracts', 'representative_id')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->unsignedInteger('representative_id')->nullable();
                $table->foreign('representative_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_tables', function (Blueprint $table) {
            //
        });
    }
};
