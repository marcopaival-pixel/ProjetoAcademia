<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingModule;
use App\Models\TrainingLesson;
use Illuminate\Support\Str;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Módulo de Boas-vindas
        $m1 = TrainingModule::create([
            'title' => '🚀 Bem-vindo ao NexShape',
            'slug' => Str::slug('Bem-vindo ao NexShape'),
            'description' => 'Aprenda a utilizar todo o potencial da nossa plataforma para acelerar seus resultados.',
            'order' => 1,
            'is_active' => true,
        ]);

        TrainingLesson::create([
            'module_id' => $m1->id,
            'title' => 'Tour pela Plataforma',
            'slug' => Str::slug('Tour pela Plataforma'),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Link de exemplo
            'content' => 'Nesta aula, mostraremos como navegar por todas as funcionalidades do NexShape, desde seus treinos até a comunidade social.',
            'order' => 1,
            'is_active' => true,
        ]);

        TrainingLesson::create([
            'module_id' => $m1->id,
            'title' => 'Configurando seu Perfil e Metas',
            'slug' => Str::slug('Configurando seu Perfil e Metas'),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'content' => 'Aprenda a configurar seus dados pessoais, bioimpedância e definir suas metas de curto e longo prazo.',
            'order' => 2,
            'is_active' => true,
        ]);

        // 2. Módulo de Nutrição
        $m2 = TrainingModule::create([
            'title' => '🍎 Nutrição e Performance',
            'slug' => Str::slug('Nutricao e Performance'),
            'description' => 'A base de qualquer resultado sólido começa na cozinha. Entenda como comer para vencer.',
            'order' => 2,
            'is_active' => true,
        ]);

        TrainingLesson::create([
            'module_id' => $m2->id,
            'title' => 'O Poder dos Macronutrientes',
            'slug' => Str::slug('O Poder dos Macronutrientes'),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'content' => 'Entenda o papel das Proteínas, Carboidratos e Gorduras na sua dieta e como o NexShape ajuda a monitorá-los.',
            'order' => 1,
            'is_active' => true,
        ]);

        TrainingLesson::create([
            'module_id' => $m2->id,
            'title' => 'Como utilizar o Scanner de Alimentos',
            'slug' => Str::slug('Como utilizar o Scanner de Alimentos'),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'content' => 'Veja como é fácil registrar suas refeições usando a câmera do seu celular e nossa inteligência artificial.',
            'order' => 2,
            'is_active' => true,
        ]);

        // 3. Módulo de Treinamento
        $m3 = TrainingModule::create([
            'title' => '💪 Treinamento Inteligente',
            'slug' => Str::slug('Treinamento Inteligente'),
            'description' => 'Dicas técnicas para melhorar sua execução e evitar lesões.',
            'order' => 3,
            'is_active' => true,
        ]);

        TrainingLesson::create([
            'module_id' => $m3->id,
            'title' => 'Entendendo a RPE (Esforço Percebido)',
            'slug' => Str::slug('Entendendo a RPE'),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'content' => 'Aprenda a classificar a intensidade do seu treino para que seu treinador possa ajustar sua carga com precisão.',
            'order' => 1,
            'is_active' => true,
        ]);
    }
}
