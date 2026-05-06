<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientService
{
    /**
     * Cadastra ou vincula um paciente a uma organização.
     * Implementa a lógica de deduplicação global por CPF.
     */
    public function registerPatient(array $data, int $organizationId, ?int $professionalId = null)
    {
        return DB::transaction(function () use ($data, $organizationId, $professionalId) {
            // 1. Verificar se o paciente já existe globalmente pelo CPF
            $patient = Patient::where('cpf', \App\Support\Cpf::normalize($data['cpf']))->first();

            if (!$patient) {
                // 2. Criar novo registro global se não existir
                $patient = Patient::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $data['name'],
                    'cpf' => \App\Support\Cpf::normalize($data['cpf']),
                    'email' => $data['email'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                    'gender' => $data['gender'] ?? null,
                ]);
            }

            // 3. Criar o vínculo com a Organização (Tenant Isolation)
            // O uso de firstOrCreate garante que não duplicamos o vínculo se ele já existir
            $vincule = DB::table('organization_patient')->updateOrInsert(
                [
                    'organization_id' => $organizationId,
                    'patient_id' => $patient->id
                ],
                [
                    'internal_code' => $data['internal_code'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            // 4. Se houver um profissional, cria o vínculo específico (ABAC)
            if ($professionalId) {
                DB::table('organization_professional_patient')->updateOrInsert(
                    [
                        'organization_id' => $organizationId,
                        'professional_id' => $professionalId,
                        'patient_id' => $patient->id
                    ],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }

            return $patient;
        });
    }

    /**
     * Retorna os pacientes de uma organização, respeitando o isolamento.
     */
    public function getPatientsByOrganization(int $organizationId, ?int $professionalId = null)
    {
        $query = Patient::query()
            ->join('organization_patient', 'patients.id', '=', 'organization_patient.patient_id')
            ->where('organization_patient.organization_id', $organizationId);

        // Se for um profissional limitado a seus próprios pacientes
        if ($professionalId) {
            $query->join('organization_professional_patient', function ($join) use ($organizationId, $professionalId) {
                $join->on('patients.id', '=', 'organization_professional_patient.patient_id')
                    ->where('organization_professional_patient.organization_id', '=', $organizationId)
                    ->where('organization_professional_patient.professional_id', '=', $professionalId);
            });
        }

        return $query->select('patients.*', 'organization_patient.internal_code')->get();
    }
}
