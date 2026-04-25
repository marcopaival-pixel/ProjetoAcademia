<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $menus = [
            ['name' => 'evolution', 'label' => 'Galeria', 'route' => 'evolution.index', 'icon' => 'fas fa-images', 'order' => 15, 'is_required' => false],
            ['name' => 'progression.plans', 'label' => 'Planos de Treino', 'route' => 'progression.plans.index', 'icon' => 'fas fa-dumbbell', 'order' => 16, 'is_required' => false],
            ['name' => 'progression.charts', 'label' => 'Gráficos de Progressão', 'route' => 'progression.charts', 'icon' => 'fas fa-chart-line', 'order' => 17, 'is_required' => false],
            ['name' => 'trophies', 'label' => 'Conquistas', 'route' => 'trophies.index', 'icon' => 'fas fa-medal', 'order' => 18, 'is_required' => false],
            ['name' => 'body-analysis', 'label' => 'Análise Corporal IA', 'route' => 'body-analysis.index', 'icon' => 'fas fa-brain', 'order' => 19, 'is_required' => false],
            ['name' => 'menu.preferences', 'label' => 'Personalizar Menu', 'route' => 'menu.preferences.index', 'icon' => 'fas fa-th-large', 'order' => 100, 'is_required' => true],
            ['name' => 'patient.professionals.search', 'label' => 'Vincular Profissional', 'route' => 'patient.professionals.search', 'icon' => 'fas fa-user-plus', 'order' => 101, 'is_required' => false],
            ['name' => 'active-rest', 'label' => 'Descanso Ativo', 'route' => 'active-rest.index', 'icon' => 'fas fa-leaf', 'order' => 10, 'is_required' => false],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['name' => $menu['name']], array_merge($menu, ['portal' => 'app']));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed as we're just seeding data
    }
};
