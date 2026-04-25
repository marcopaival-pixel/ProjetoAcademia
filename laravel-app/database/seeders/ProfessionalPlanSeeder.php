<?php

namespace Database\Seeders;

use App\Models\ProfessionalPlan;
use Illuminate\Database\Seeder;

class ProfessionalPlanSeeder extends Seeder
{
    public function run(): void
    {
        ProfessionalPlan::updateOrCreate(['name' => 'Básico'], ['max_patients' => 50]);
        ProfessionalPlan::updateOrCreate(['name' => 'Profissional'], ['max_patients' => 200]);
        ProfessionalPlan::updateOrCreate(['name' => 'Premium'], ['max_patients' => -1]); // Ilimitado
    }
}
