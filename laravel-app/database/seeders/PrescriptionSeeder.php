<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidade;
use App\Models\PrescriptionTemplate;
use App\Models\ClinicProtocol;
use App\Models\AcademyCompany;
use App\Models\User;

class PrescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $company = AcademyCompany::first();
        if (!$company) return;

        $specialties = Especialidade::all();
        $professional = User::whereHas('roles', fn($q) => $q->where('name', 'professional'))->first();

        if (!$professional || $specialties->isEmpty()) return;

        foreach ($specialties as $s) {
            // Template
            PrescriptionTemplate::create([
                'especialidade_id' => $s->id,
                'professional_id' => $professional->id,
                'title' => "Template Padrão {$s->nome}",
                'content' => "Protocolo de atendimento focado em {$s->nome} para otimização de resultados.",
            ]);

            // Clinic Protocol
            ClinicProtocol::create([
                'academy_company_id' => $company->id,
                'especialidade_id' => $s->id,
                'type' => 'medical', // Padrão para este seeder
                'name' => "Protocolo Institucional: {$s->nome}",
                'description' => "Diretrizes clínicas da instituição para a especialidade de {$s->nome}.",
                'objective' => "Redução de sintomas e melhora funcional",
                'protocol' => "Diretriz v3.1 NexShape",
                'frequency' => "12/12h",
                'duration' => "15 dias",
            ]);
        }
    }
}
