<?php

namespace Database\Seeders;

use App\Models\AppFeature;
use App\Models\UpgradePopup;
use Illuminate\Database\Seeder;

class WorkoutImportFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $feature = AppFeature::updateOrCreate(
            ['code' => 'workout_import_photo'],
            [
                'name' => 'Importar Treino por Foto',
                'category' => 'premium',
                'description' => 'Permite digitalizar fichas de treino físicas usando IA e OCR.',
                'is_active' => true,
                'show_lock' => true,
                'show_badge' => true,
            ]
        );

        UpgradePopup::updateOrCreate(
            ['feature_code' => 'workout_import_photo'],
            [
                'title' => 'Digitalize seus treinos com IA',
                'message' => 'Chega de digitar exercício por exercício. Tire uma foto da sua ficha e deixe o NexShape fazer o trabalho pesado para você.',
                'benefits' => [
                    'Extração instantânea de exercícios',
                    'Detecção automática de séries e reps',
                    'Integração com catálogo de exercícios',
                    'Histórico de importações'
                ],
                'button_text' => 'Upgrade para Premium',
                'image_url' => '/images/ai-workout-import.png',
            ]
        );
    }
}
