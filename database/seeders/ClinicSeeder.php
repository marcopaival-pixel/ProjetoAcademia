<?php

namespace Database\Seeders;

use App\Models\AcademyCompany;
use App\Models\Clinic;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        $company = AcademyCompany::first();
        if (!$company) return;

        Clinic::updateOrCreate(
            ['slug' => 'clinica-viva-bem'],
            [
                'academy_company_id' => $company->id,
                'name' => 'Clínica Viva Bem',
                'primary_color' => '#10b981',
                'is_active' => true,
            ]
        );

        Clinic::updateOrCreate(
            ['slug' => 'clinica-corpo-saudavel'],
            [
                'academy_company_id' => $company->id,
                'name' => 'Clínica Corpo Saudável',
                'primary_color' => '#3b82f6',
                'is_active' => true,
            ]
        );
    }
}
