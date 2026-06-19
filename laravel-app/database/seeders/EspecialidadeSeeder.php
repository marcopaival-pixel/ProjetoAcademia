<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidade;
use App\Models\Profession;

class EspecialidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First ensure professions exist to get their IDs
        $profEdu = Profession::where('name', 'Educador Físico')->first();
        $profNutri = Profession::where('name', 'Nutricionista')->first();
        $profFisio = Profession::where('name', 'Fisioterapeuta')->first();
        $profMed = Profession::where('name', 'Médico')->first();
        $profPsi = Profession::where('name', 'Psicólogo')->first();

        $data = [];

        if ($profEdu) {
            $data = array_merge($data, [
                ['codigo' => 'EDU-PT', 'nome' => 'Personal Trainer', 'categoria' => 'Treinamento', 'icone' => 'fas fa-dumbbell', 'profession_id' => $profEdu->id],
                ['codigo' => 'EDU-PF', 'nome' => 'Preparador Físico', 'categoria' => 'Treinamento', 'icone' => 'fas fa-running', 'profession_id' => $profEdu->id],
                ['codigo' => 'EDU-TE', 'nome' => 'Treinador Esportivo', 'categoria' => 'Esporte', 'icone' => 'fas fa-whistle', 'profession_id' => $profEdu->id],
            ]);
        }

        if ($profNutri) {
            $data = array_merge($data, [
                ['codigo' => 'NUT-NE', 'nome' => 'Nutrição Esportiva', 'categoria' => 'Nutrição', 'icone' => 'fas fa-apple-alt', 'profession_id' => $profNutri->id],
                ['codigo' => 'NUT-EM', 'nome' => 'Emagrecimento', 'categoria' => 'Nutrição', 'icone' => 'fas fa-weight', 'profession_id' => $profNutri->id],
                ['codigo' => 'NUT-PE', 'nome' => 'Performance', 'categoria' => 'Nutrição', 'icone' => 'fas fa-tachometer-alt', 'profession_id' => $profNutri->id],
            ]);
        }

        if ($profFisio) {
            $data = array_merge($data, [
                ['codigo' => 'FIS-FE', 'nome' => 'Fisioterapia Esportiva', 'categoria' => 'Reabilitação', 'icone' => 'fas fa-heartbeat', 'profession_id' => $profFisio->id],
                ['codigo' => 'FIS-RE', 'nome' => 'Reabilitação', 'categoria' => 'Reabilitação', 'icone' => 'fas fa-crutch', 'profession_id' => $profFisio->id],
                ['codigo' => 'FIS-TO', 'nome' => 'Traumato-Ortopedia', 'categoria' => 'Reabilitação', 'icone' => 'fas fa-bone', 'profession_id' => $profFisio->id],
            ]);
        }

        if ($profMed) {
            $data = array_merge($data, [
                ['codigo' => 'MED-ME', 'nome' => 'Medicina do Esporte', 'categoria' => 'Medicina', 'icone' => 'fas fa-user-md', 'profession_id' => $profMed->id],
                ['codigo' => 'MED-EE', 'nome' => 'Endocrinologia Esportiva', 'categoria' => 'Medicina', 'icone' => 'fas fa-vial', 'profession_id' => $profMed->id],
                ['codigo' => 'MED-OE', 'nome' => 'Ortopedia Esportiva', 'categoria' => 'Medicina', 'icone' => 'fas fa-x-ray', 'profession_id' => $profMed->id],
                ['codigo' => 'MED-NUE', 'nome' => 'Nutrologia Esportiva', 'categoria' => 'Medicina', 'icone' => 'fas fa-leaf', 'profession_id' => $profMed->id],
                ['codigo' => 'MED-CE', 'nome' => 'Cardiologia Esportiva', 'categoria' => 'Medicina', 'icone' => 'fas fa-heart', 'profession_id' => $profMed->id],
            ]);
        }

        if ($profPsi) {
            $data = array_merge($data, [
                ['codigo' => 'PSI-PE', 'nome' => 'Psicologia Esportiva', 'categoria' => 'Psicologia', 'icone' => 'fas fa-brain', 'profession_id' => $profPsi->id],
            ]);
        }

        foreach ($data as $item) {
            Especialidade::updateOrCreate(['codigo' => $item['codigo']], array_merge($item, ['status' => 'Ativo']));
        }
    }
}
