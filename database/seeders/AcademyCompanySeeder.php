<?php

namespace Database\Seeders;

use App\Models\AcademyCompany;
use App\Models\AcademyUnit;
use Illuminate\Database\Seeder;

class AcademyCompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = AcademyCompany::firstOrCreate(
            ['slug' => 'principal'],
            [
                'name' => 'Clínica Modelo NexShape',
                'legal_name' => 'NexShape Health & Fitness Ltda',
                'tax_id' => '12.345.678/0001-99',
                'responsible_name' => 'Administrador Modelo',
                'responsible_email' => 'admin@nexshape.com.br',
                'phone' => '(11) 99999-9999',
                'address' => 'Av. Paulista, 1000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01310-100',
                'is_active' => true,
                'onboarding_status' => 'completed',
            ]
        );

        AcademyUnit::firstOrCreate(
            [
                'academy_company_id' => $company->id,
                'code' => 'MATRIZ',
            ],
            [
                'name' => 'Unidade matriz',
                'is_active' => true,
            ]
        );
    }
}
