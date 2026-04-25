<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\ProfessionalProfile;
use App\Models\TrainingPlan;
use App\Models\Role;
use App\Models\Plan;
use App\Models\Especialidade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BulkImportService
{
    /**
     * Import data from CSV.
     */
    public function import(string $module, string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle, 0, ';');

        $results = [
            'success_count' => 0,
            'error_count' => 0,
            'errors' => []
        ];

        $rowCount = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowCount++;
            
            // Basic data cleaning
            $row = array_map('trim', $row);

            try {
                DB::beginTransaction();

                switch ($module) {
                    case 'pacientes':
                        $this->importPaciente($row, $rowCount);
                        break;
                    case 'profissionais':
                        $this->importProfessional($row, $rowCount);
                        break;
                    case 'alunos':
                        $this->importAluno($row, $rowCount);
                        break;
                    case 'treinos':
                        $this->importWorkout($row, $rowCount);
                        break;
                }

                DB::commit();
                $results['success_count']++;
            } catch (\Exception $e) {
                DB::rollBack();
                $results['error_count']++;
                $results['errors'][] = "Linha {$rowCount}: " . $e->getMessage();
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Import Paciente.
     */
    private function importPaciente(array $row, int $line): void
    {
        if (count($row) < 12) throw new \Exception("Dados insuficientes.");

        $nome = $row[0];
        $cpf = preg_replace('/\D/', '', $row[1]);
        $email = $row[2];
        $phone = $row[3];
        $birthDate = $row[4];
        $gender = $row[5]; // M or F
        $height = (int) $row[6];
        $weight = (float) $row[7];
        $address = $row[8];
        $city = $row[9];
        $state = $row[10];
        $status = strtolower($row[11]) === 'ativo' ? 'active' : 'inactive';
        $obs = $row[12] ?? null;
        $proRespEmail = $row[13] ?? null;

        if (empty($nome)) throw new \Exception("Nome obrigatório.");
        if (empty($cpf)) throw new \Exception("CPF obrigatório.");
        if (empty($email)) throw new \Exception("E-mail obrigatório.");

        $user = User::where('cpf', $cpf)->orWhere('email', $email)->first();

        if ($user) {
            // Se já existe, apenas verifica o vínculo com o profissional se fornecido
            if ($proRespEmail) {
                $pro = User::where('email', $proRespEmail)->first();
                if ($pro) {
                    DB::table('pacientes')->updateOrInsert(
                        ['user_id' => $user->id, 'profissional_id' => $pro->id],
                        ['status' => 'Ativo', 'data_cadastro' => now(), 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
            return;
        }

        // Criar Novo Usuário
        $user = User::create([
            'name' => $nome,
            'email' => $email,
            'cpf' => $cpf,
            'phone' => $phone,
            'status' => $status,
            'password_hash' => Hash::make(Str::random(12)),
            'registration_approval_status' => 'approved',
        ]);

        $role = Role::where('name', 'paciente')->first();
        if ($role) $user->roles()->attach($role->id);

        UserProfile::create([
            'user_id' => $user->id,
            'birth_date' => $this->parseDate($birthDate),
            'sex' => strtoupper($gender),
            'height_cm' => $height,
            'target_weight_kg' => $weight,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'disease_details' => $obs,
        ]);

        if ($proRespEmail) {
            $pro = User::where('email', $proRespEmail)->first();
            if ($pro) {
                DB::table('pacientes')->insert([
                    'user_id' => $user->id,
                    'profissional_id' => $pro->id,
                    'status' => 'Ativo',
                    'data_cadastro' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Import Professional.
     */
    private function importProfessional(array $row, int $line): void
    {
        if (count($row) < 7) throw new \Exception("Dados insuficientes.");

        $nome = $row[0];
        $cpf = preg_replace('/\D/', '', $row[1]);
        $email = $row[2];
        $phone = $row[3];
        $especialidadeNome = $row[4];
        $regNumber = $row[5];
        $status = strtolower($row[6]) === 'ativo' ? 'active' : 'inactive';

        if (empty($nome)) throw new \Exception("Nome obrigatório.");
        if (empty($cpf)) throw new \Exception("CPF obrigatório.");
        if (empty($email)) throw new \Exception("E-mail obrigatório.");
        if (empty($regNumber)) throw new \Exception("Registro profissional obrigatório.");

        if (User::where('cpf', $cpf)->exists()) throw new \Exception("CPF já cadastrado.");
        if (User::where('email', $email)->exists()) throw new \Exception("E-mail já cadastrado.");

        $especialidade = Especialidade::where('nome', $especialidadeNome)->first();
        if (!$especialidade) throw new \Exception("Especialidade '{$especialidadeNome}' não encontrada.");

        $user = User::create([
            'name' => $nome,
            'email' => $email,
            'cpf' => $cpf,
            'phone' => $phone,
            'status' => $status,
            'password_hash' => Hash::make(Str::random(12)),
            'registration_approval_status' => 'approved',
        ]);

        $role = Role::where('name', 'professional')->first();
        if ($role) $user->roles()->attach($role->id);

        ProfessionalProfile::create([
            'user_id' => $user->id,
            'specialty' => $especialidade->nome,
            'registration_number' => $regNumber,
            'address' => $row[8] ?? null,
            'city' => $row[9] ?? null,
            'state' => $row[10] ?? null,
        ]);
    }

    /**
     * Import Aluno.
     */
    private function importAluno(array $row, int $line): void
    {
        if (count($row) < 10) throw new \Exception("Dados insuficientes.");

        $nome = $row[0];
        $cpf = preg_replace('/\D/', '', $row[1]);
        $email = $row[2];
        $phone = $row[3];
        $birthDate = $row[4];
        $gender = $row[5];
        $height = (int) $row[6];
        $weight = (float) $row[7];
        $goalLabel = $row[8];
        $status = strtolower($row[9]) === 'ativo' ? 'active' : 'inactive';

        if (empty($nome)) throw new \Exception("Nome obrigatório.");
        if (empty($cpf)) throw new \Exception("CPF obrigatório.");
        if (empty($email)) throw new \Exception("E-mail obrigatório.");

        if (User::where('cpf', $cpf)->exists()) throw new \Exception("CPF já cadastrado.");
        if (User::where('email', $email)->exists()) throw new \Exception("E-mail já cadastrado.");

        $user = User::create([
            'name' => $nome,
            'email' => $email,
            'cpf' => $cpf,
            'phone' => $phone,
            'status' => $status,
            'password_hash' => Hash::make(Str::random(12)),
            'registration_approval_status' => 'approved',
        ]);

        $role = Role::where('name', 'aluno')->first();
        if ($role) $user->roles()->attach($role->id);

        // Map goal label to key if possible
        $goals = UserProfile::getAvailableGoals();
        $goalKey = 'maintain';
        foreach ($goals as $key => $g) {
            if (stripos($g['label'], $goalLabel) !== false) {
                $goalKey = $key;
                break;
            }
        }

        UserProfile::create([
            'user_id' => $user->id,
            'birth_date' => $this->parseDate($birthDate),
            'sex' => strtoupper($gender),
            'height_cm' => $height,
            'target_weight_kg' => $weight,
            'goal' => $goalKey,
            'disease_details' => $row[10] ?? null,
        ]);
        
        // Handle plano if provided
        if (!empty($row[11])) {
            $plan = Plan::where('name', 'LIKE', "%{$row[11]}%")->first();
            if ($plan) {
                $user->update(['plan_id' => $plan->id]);
            }
        }
    }

    /**
     * Import Workout (TrainingPlan).
     */
    private function importWorkout(array $row, int $line): void
    {
        if (count($row) < 6) throw new \Exception("Dados insuficientes.");

        $nome = $row[0];
        $desc = $row[1];
        $nivel = $row[2]; // Iniciante, Intermediário, Avançado
        $tipo = $row[3];
        $duracao = (int) $row[4];
        $status = strtolower($row[5]) === 'ativo' ? 1 : 0;

        if (empty($nome)) throw new \Exception("Nome do treino obrigatório.");
        
        $validLevels = ['Iniciante', 'Intermediário', 'Avançado'];
        if (!in_array($nivel, $validLevels)) throw new \Exception("Nível inválido. Use: Iniciante, Intermediário ou Avançado.");

        TrainingPlan::create([
            'user_id' => auth()->id(), // Admin as creator
            'name' => $nome,
            'description' => $desc,
            'difficulty' => $nivel,
            'plan_label' => strtoupper(substr($tipo, 0, 3)),
            'estimated_duration' => $duracao,
            'is_active' => $status,
            'goal' => $row[6] ?? null,
        ]);
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate(string $date): ?string
    {
        if (empty($date)) return null;
        try {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}
