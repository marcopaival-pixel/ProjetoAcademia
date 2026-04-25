<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EspecialidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['codigo' => 'FIT001', 'nome' => 'Personal Trainer', 'categoria' => 'Fitness', 'icone' => 'fas fa-dumbbell', 'profession_id' => 1],
            ['codigo' => 'NUT001', 'nome' => 'Nutricionista Clínico', 'categoria' => 'Saúde', 'icone' => 'fas fa-apple-alt', 'profession_id' => 2],
            ['codigo' => 'NUT002', 'nome' => 'Nutricionista Esportivo', 'categoria' => 'Saúde', 'icone' => 'fas fa-running', 'profession_id' => 2],
            ['codigo' => 'FIS001', 'nome' => 'Fisioterapeuta Desportivo', 'categoria' => 'Reabilitação', 'icone' => 'fas fa-heartbeat', 'profession_id' => 3],
            ['codigo' => 'MED001', 'nome' => 'Médico Nutrólogo', 'categoria' => 'Saúde', 'icone' => 'fas fa-user-md', 'profession_id' => 4],
        ];

        foreach ($data as $item) {
            \App\Models\Especialidade::updateOrCreate(['codigo' => $item['codigo']], array_merge($item, ['status' => 'Ativo']));
        }
    }
}
