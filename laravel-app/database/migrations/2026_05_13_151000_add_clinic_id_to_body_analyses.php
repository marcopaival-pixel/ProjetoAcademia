<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('body_analyses', 'clinic_id')) {
                $table->unsignedBigInteger('clinic_id')->nullable()->after('id');
                $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('body_analyses', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });
    }
};
