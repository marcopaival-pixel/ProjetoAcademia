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
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->string('responsible_name')->nullable()->after('tax_id');
            $table->string('responsible_email')->nullable()->after('responsible_name');
            $table->string('phone', 32)->nullable()->after('responsible_email');
            $table->string('address')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 2)->nullable()->after('city');
            $table->string('zip_code', 20)->nullable()->after('state');
            
            // Note: Making tax_id unique might fail if there are existing duplicates.
            // In a real scenario, we'd clean up data first.
            $table->string('tax_id', 64)->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->dropColumn([
                'responsible_name', 'responsible_email', 'phone', 
                'address', 'city', 'state', 'zip_code'
            ]);
            $table->string('tax_id', 64)->nullable()->change();
            $table->dropUnique(['tax_id']);
        });
    }
};
