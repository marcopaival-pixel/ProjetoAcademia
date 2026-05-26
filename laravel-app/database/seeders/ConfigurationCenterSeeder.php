<?php

namespace Database\Seeders;

use App\Models\AdminEntity;
use App\Models\AdminField;
use Illuminate\Database\Seeder;

class ConfigurationCenterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Grupos Musculares
        $muscles = AdminEntity::create([
            'name' => 'muscles',
            'display_name' => 'Grupos Musculares',
            'table_name' => 'muscles',
            'model_class' => 'App\Models\Muscle',
            'icon' => 'heroicon-o-user',
            'category' => 'Cadastros Mestre',
        ]);

        $muscles->fields()->createMany([
            ['name' => 'name', 'label' => 'Nome', 'type' => 'text', 'is_required' => true, 'is_searchable' => true, 'sort_order' => 1],
            ['name' => 'group_id', 'label' => 'ID do Grupo', 'type' => 'number', 'sort_order' => 2],
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'text', 'sort_order' => 3],
            ['name' => 'is_active', 'label' => 'Ativo', 'type' => 'boolean', 'sort_order' => 4],
        ]);

        // 2. Exercícios
        $exercises = AdminEntity::create([
            'name' => 'exercises',
            'display_name' => 'Exercícios',
            'table_name' => 'exercises_catalog',
            'model_class' => 'App\Models\ExerciseCatalog',
            'icon' => 'heroicon-o-fire',
            'category' => 'Cadastros Mestre',
        ]);

        $exercises->fields()->createMany([
            ['name' => 'name', 'label' => 'Nome', 'type' => 'text', 'is_required' => true, 'is_searchable' => true, 'sort_order' => 1],
            ['name' => 'muscle_group', 'label' => 'Grupo Muscular', 'type' => 'text', 'is_filterable' => true, 'sort_order' => 2],
            ['name' => 'equipment', 'label' => 'Equipamento', 'type' => 'text', 'sort_order' => 3],
            ['name' => 'difficulty', 'label' => 'Dificuldade', 'type' => 'select', 'options' => ['choices' => ['Easy' => 'Iniciante', 'Medium' => 'Intermediário', 'Hard' => 'Avançado']], 'sort_order' => 4],
            ['name' => 'instructions', 'label' => 'Instruções', 'type' => 'textarea', 'sort_order' => 5],
            ['name' => 'video_url', 'label' => 'URL do Vídeo', 'type' => 'text', 'sort_order' => 6],
            ['name' => 'is_active', 'label' => 'Ativo', 'type' => 'boolean', 'sort_order' => 7],
        ]);

    }
}
