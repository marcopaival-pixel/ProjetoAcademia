<?php

namespace Database\Seeders;

use App\Models\Muscle;
use App\Models\MuscleGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MuscleSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks for clean seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('muscles')->truncate();
        DB::table('muscle_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $groups = [
            ['id' => 1, 'name' => 'Ombros', 'region' => 'Membros Superiores'],
            ['id' => 2, 'name' => 'Braços', 'region' => 'Membros Superiores'],
            ['id' => 3, 'name' => 'Antebraços', 'region' => 'Membros Superiores'],
            ['id' => 4, 'name' => 'Peitoral', 'region' => 'Tronco Anterior'],
            ['id' => 5, 'name' => 'Abdômen', 'region' => 'Tronco Anterior'],
            ['id' => 6, 'name' => 'Costas', 'region' => 'Tronco Posterior'],
            ['id' => 7, 'name' => 'Lombar', 'region' => 'Tronco Posterior'],
            ['id' => 8, 'name' => 'Escápula', 'region' => 'Tronco Posterior'],
            ['id' => 9, 'name' => 'Quadríceps', 'region' => 'Membros Inferiores'],
            ['id' => 10, 'name' => 'Posterior de Coxa', 'region' => 'Membros Inferiores'],
            ['id' => 11, 'name' => 'Glúteos', 'region' => 'Membros Inferiores'],
            ['id' => 12, 'name' => 'Adutores', 'region' => 'Membros Inferiores'],
            ['id' => 13, 'name' => 'Abdutores', 'region' => 'Membros Inferiores'],
            ['id' => 14, 'name' => 'Panturrilhas', 'region' => 'Membros Inferiores'],
            ['id' => 15, 'name' => 'Canela', 'region' => 'Membros Inferiores'],
            ['id' => 16, 'name' => 'Core', 'region' => 'Estabilizadores'],
        ];

        foreach ($groups as $group) {
            MuscleGroup::create($group);
        }

        $muscles = [
            // Ombros
            ['id' => 1, 'group_id' => 1, 'name' => 'Deltoide Anterior', 'type' => 'Primário'],
            ['id' => 2, 'group_id' => 1, 'name' => 'Deltoide Lateral', 'type' => 'Primário'],
            ['id' => 3, 'group_id' => 1, 'name' => 'Deltoide Posterior', 'type' => 'Primário'],
            ['id' => 4, 'group_id' => 1, 'name' => 'Manguito Rotador', 'type' => 'Estabilizador'],
            // Braços
            ['id' => 5, 'group_id' => 2, 'name' => 'Bíceps Braquial', 'type' => 'Primário'],
            ['id' => 6, 'group_id' => 2, 'name' => 'Braquial', 'type' => 'Secundário'],
            ['id' => 7, 'group_id' => 2, 'name' => 'Tríceps Braquial', 'type' => 'Primário'],
            // Antebraços
            ['id' => 8, 'group_id' => 3, 'name' => 'Braquiorradial', 'type' => 'Secundário'],
            ['id' => 9, 'group_id' => 3, 'name' => 'Flexores do Pulso', 'type' => 'Secundário'],
            ['id' => 10, 'group_id' => 3, 'name' => 'Extensores do Pulso', 'type' => 'Secundário'],
            // Peitoral
            ['id' => 11, 'group_id' => 4, 'name' => 'Peitoral Superior', 'type' => 'Primário'],
            ['id' => 12, 'group_id' => 4, 'name' => 'Peitoral Médio', 'type' => 'Primário'],
            ['id' => 13, 'group_id' => 4, 'name' => 'Peitoral Inferior', 'type' => 'Primário'],
            ['id' => 14, 'group_id' => 4, 'name' => 'Peitoral Menor', 'type' => 'Secundário'],
            // Abdômen
            ['id' => 15, 'group_id' => 5, 'name' => 'Reto Abdominal Superior', 'type' => 'Primário'],
            ['id' => 16, 'group_id' => 5, 'name' => 'Reto Abdominal Inferior', 'type' => 'Primário'],
            ['id' => 17, 'group_id' => 5, 'name' => 'Oblíquo Externo', 'type' => 'Primário'],
            ['id' => 18, 'group_id' => 5, 'name' => 'Oblíquo Interno', 'type' => 'Primário'],
            ['id' => 19, 'group_id' => 5, 'name' => 'Transverso do Abdômen', 'type' => 'Estabilizador'],
            ['id' => 20, 'group_id' => 5, 'name' => 'Serrátil Anterior', 'type' => 'Secundário'],
            // Costas
            ['id' => 21, 'group_id' => 6, 'name' => 'Latíssimo do Dorso', 'type' => 'Primário'],
            ['id' => 22, 'group_id' => 6, 'name' => 'Trapézio Superior', 'type' => 'Primário'],
            ['id' => 23, 'group_id' => 6, 'name' => 'Trapézio Médio', 'type' => 'Primário'],
            ['id' => 24, 'group_id' => 6, 'name' => 'Trapézio Inferior', 'type' => 'Primário'],
            ['id' => 25, 'group_id' => 6, 'name' => 'Romboide Maior', 'type' => 'Secundário'],
            ['id' => 26, 'group_id' => 6, 'name' => 'Romboide Menor', 'type' => 'Secundário'],
            // Lombar
            ['id' => 27, 'group_id' => 7, 'name' => 'Eretores da Espinha', 'type' => 'Estabilizador'],
            ['id' => 28, 'group_id' => 7, 'name' => 'Quadrado Lombar', 'type' => 'Estabilizador'],
            // Escápula
            ['id' => 29, 'group_id' => 8, 'name' => 'Infraespinhal', 'type' => 'Secundário'],
            ['id' => 30, 'group_id' => 8, 'name' => 'Redondo Maior', 'type' => 'Secundário'],
            ['id' => 31, 'group_id' => 8, 'name' => 'Redondo Menor', 'type' => 'Secundário'],
            // Quadríceps
            ['id' => 32, 'group_id' => 9, 'name' => 'Reto Femoral', 'type' => 'Primário'],
            ['id' => 33, 'group_id' => 9, 'name' => 'Vasto Lateral', 'type' => 'Primário'],
            ['id' => 34, 'group_id' => 9, 'name' => 'Vasto Medial', 'type' => 'Primário'],
            ['id' => 35, 'group_id' => 9, 'name' => 'Vasto Intermédio', 'type' => 'Primário'],
            ['id' => 36, 'group_id' => 9, 'name' => 'Sartório', 'type' => 'Secundário'],
            // Posterior de Coxa
            ['id' => 37, 'group_id' => 10, 'name' => 'Bíceps Femoral', 'type' => 'Primário'],
            ['id' => 38, 'group_id' => 10, 'name' => 'Semitendíneo', 'type' => 'Primário'],
            ['id' => 39, 'group_id' => 10, 'name' => 'Semimembranáceo', 'type' => 'Primário'],
            // Glúteos
            ['id' => 40, 'group_id' => 11, 'name' => 'Glúteo Maior', 'type' => 'Primário'],
            ['id' => 41, 'group_id' => 11, 'name' => 'Glúteo Médio', 'type' => 'Primário'],
            ['id' => 42, 'group_id' => 11, 'name' => 'Glúteo Menor', 'type' => 'Primário'],
            // Adutores
            ['id' => 43, 'group_id' => 12, 'name' => 'Adutor Longo', 'type' => 'Primário'],
            ['id' => 44, 'group_id' => 12, 'name' => 'Adutor Curto', 'type' => 'Primário'],
            ['id' => 45, 'group_id' => 12, 'name' => 'Adutor Magno', 'type' => 'Primário'],
            // Abdutores
            ['id' => 46, 'group_id' => 13, 'name' => 'Tensor da Fáscia Lata', 'type' => 'Secundário'],
            // Panturrilhas
            ['id' => 47, 'group_id' => 14, 'name' => 'Gastrocnêmio', 'type' => 'Primário'],
            ['id' => 48, 'group_id' => 14, 'name' => 'Sóleo', 'type' => 'Primário'],
            // Canela
            ['id' => 49, 'group_id' => 15, 'name' => 'Tibial Anterior', 'type' => 'Primário'],
            // Core
            ['id' => 50, 'group_id' => 16, 'name' => 'Psoas Ilíaco', 'type' => 'Estabilizador'],
            ['id' => 51, 'group_id' => 16, 'name' => 'Multífidos', 'type' => 'Estabilizador'],
        ];

        foreach ($muscles as $muscle) {
            Muscle::create($muscle);
        }
    }
}
