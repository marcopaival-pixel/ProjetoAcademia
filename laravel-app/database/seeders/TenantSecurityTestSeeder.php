<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AcademyCompany;
use Illuminate\Database\Seeder;

class TenantSecurityTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Criar Clínicas de Teste
        $clinicaAlpha = AcademyCompany::updateOrCreate(
            ['slug' => 'clinica-alpha'],
            [
                'name' => 'Clínica Alpha - Unidade Norte',
                'uuid' => \Str::uuid(),
                'is_active' => true,
            ]
        );

        $clinicaBeta = AcademyCompany::updateOrCreate(
            ['slug' => 'clinica-beta'],
            [
                'name' => 'Clínica Beta - Unidade Sul',
                'uuid' => \Str::uuid(),
                'is_active' => true,
            ]
        );

        // 2. Criar Usuários (Profissional, Pacientes, Aluno)
        $users = [
            [
                'email' => 'pro@test.com',
                'name' => 'Dr. João Silva',
                'role' => 'professional'
            ],
            [
                'email' => 'pac@test.com',
                'name' => 'Maria Souza (Multi-Clínica)',
                'role' => 'paciente'
            ],
            [
                'email' => 'jose@test.com',
                'name' => 'José Santos (Apenas Alpha)',
                'role' => 'paciente'
            ],
            [
                'email' => 'aluno@test.com',
                'name' => 'Carlos Aluno (Sem Clínica)',
                'role' => 'aluno'
            ],
        ];

        $createdUsers = [];

        foreach ($users as $userData) {
            $existingId = \DB::table('users')->where('email', $userData['email'])->value('id');
            
            $data = [
                'name' => $userData['name'],
                'uuid' => \Str::uuid(),
                'password_hash' => \Hash::make('123456'),
                'status' => 'active',
                'email_verified_at' => now(),
                'registration_approval_status' => 'approved',
                'updated_at' => now(),
            ];

            if ($existingId) {
                \DB::table('users')->where('id', $existingId)->update($data);
                $userId = $existingId;
            } else {
                $data['email'] = $userData['email'];
                $data['created_at'] = now();
                $userId = \DB::table('users')->insertGetId($data);
            }

            $user = User::find($userId);
            $user->assignRole($userData['role']);
            $createdUsers[$userData['email']] = $user;
        }

        $profissional = $createdUsers['pro@test.com'];
        $maria = $createdUsers['pac@test.com'];
        $jose = $createdUsers['jose@test.com'];

        // 3. Criar Vínculos (Multi-tenant M:N)
        
        // Dr. João trabalha em ambas
        $this->linkUserToClinic($profissional, $clinicaAlpha, 'professional');
        $this->linkUserToClinic($profissional, $clinicaBeta, 'professional');

        // Maria é paciente em ambas
        $this->linkUserToClinic($maria, $clinicaAlpha, 'patient');
        $this->linkUserToClinic($maria, $clinicaBeta, 'patient');

        // José é paciente apenas na Alpha
        $this->linkUserToClinic($jose, $clinicaAlpha, 'patient');

        // 4. Criar Dados Clínicos Isolados (Tratamentos)
        
        // Maria na Alpha
        \DB::table('patient_treatment_plans')->insert([
            'patient_id' => $maria->id,
            'professional_id' => $profissional->id,
            'academy_company_id' => $clinicaAlpha->id,
            'diagnosis' => 'Tratamento Maria na Clínica ALPHA',
            'care_plan' => 'Fisioterapia 2x por semana',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Maria na Beta
        \DB::table('patient_treatment_plans')->insert([
            'patient_id' => $maria->id,
            'professional_id' => $profissional->id,
            'academy_company_id' => $clinicaBeta->id,
            'diagnosis' => 'Tratamento Maria na Clínica BETA',
            'care_plan' => 'Hidroginástica 3x por semana',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // José na Alpha
        \DB::table('patient_treatment_plans')->insert([
            'patient_id' => $jose->id,
            'professional_id' => $profissional->id,
            'academy_company_id' => $clinicaAlpha->id,
            'diagnosis' => 'Tratamento José na Clínica ALPHA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function linkUserToClinic($user, $clinic, $role)
    {
        \DB::table('clinic_user')->updateOrInsert(
            [
                'user_id' => $user->id,
                'academy_company_id' => $clinic->id,
                'role' => $role
            ],
            [
                'uuid' => \Str::uuid(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
