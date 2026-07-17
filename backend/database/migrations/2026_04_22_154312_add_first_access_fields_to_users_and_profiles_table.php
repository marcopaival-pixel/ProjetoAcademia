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
            if (!Schema::hasColumn('users', 'perfil_paciente_completo')) {
                $table->boolean('perfil_paciente_completo')->default(false)->after('onboarding_status');
            }
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->boolean('has_disease')->default(false);
            $table->text('disease_details')->nullable();
            $table->boolean('has_injury')->default(false);
            $table->text('injury_details')->nullable();
            $table->boolean('uses_medication')->default(false);
            $table->text('medication_details')->nullable();
            $table->boolean('has_allergy')->default(false);
            $table->text('allergy_details')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamp('profile_completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'address', 'city', 'state', 'has_disease', 'disease_details',
                'has_injury', 'injury_details', 'uses_medication', 'medication_details',
                'has_allergy', 'allergy_details', 'emergency_contact_name', 
                'emergency_contact_phone', 'profile_completed_at'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('perfil_paciente_completo');
        });
    }
};
