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
        Schema::table('body_assessments', function (Blueprint $table) {
            // Água Corporal
            $table->decimal('icw_l', 5, 2)->nullable()->after('muscle_percent'); // Água Intracelular
            $table->decimal('ecw_l', 5, 2)->nullable()->after('icw_l'); // Água Extracelular
            
            // Composição Detalhada
            $table->decimal('dry_lean_mass_kg', 6, 2)->nullable()->after('ecw_l');
            $table->decimal('body_fat_mass_kg', 6, 2)->nullable()->after('dry_lean_mass_kg');
            
            // Análise Segmental (Massa Magra em kg)
            $table->decimal('segmental_lean_arm_l', 5, 2)->nullable()->after('body_fat_mass_kg');
            $table->decimal('segmental_lean_arm_r', 5, 2)->nullable()->after('segmental_lean_arm_l');
            $table->decimal('segmental_lean_leg_l', 5, 2)->nullable()->after('segmental_lean_arm_r');
            $table->decimal('segmental_lean_leg_r', 5, 2)->nullable()->after('segmental_lean_leg_l');
            $table->decimal('segmental_lean_trunk', 5, 2)->nullable()->after('segmental_lean_leg_r');
            
            // Índices Clínicos
            $table->integer('visceral_fat_level')->nullable()->after('segmental_lean_trunk');
            $table->integer('basal_metabolic_rate')->nullable()->after('visceral_fat_level');
            $table->decimal('phase_angle', 4, 2)->nullable()->after('basal_metabolic_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('body_assessments', function (Blueprint $table) {
            $table->dropColumn([
                'icw_l', 'ecw_l', 'dry_lean_mass_kg', 'body_fat_mass_kg',
                'segmental_lean_arm_l', 'segmental_lean_arm_r',
                'segmental_lean_leg_l', 'segmental_lean_leg_r',
                'segmental_lean_trunk', 'visceral_fat_level',
                'basal_metabolic_rate', 'phase_angle'
            ]);
        });
    }
};
