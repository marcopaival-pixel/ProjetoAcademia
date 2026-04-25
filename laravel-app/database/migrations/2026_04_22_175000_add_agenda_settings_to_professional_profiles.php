<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->integer('appointment_duration')->default(60)->after('service_types'); // in minutes
            $table->integer('appointment_interval')->default(15)->after('appointment_duration'); // in minutes
        });
    }

    public function down(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn(['appointment_duration', 'appointment_interval']);
        });
    }
};
