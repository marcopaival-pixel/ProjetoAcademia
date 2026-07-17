<?php

namespace Database\Seeders;

use App\Models\CommunitySticker;
use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        // Stickers
        $stickers = [
            ['name' => 'Go Hard', 'path' => 'images/stickers/go_hard.png', 'category' => 'Training'],
            ['name' => 'Eat Clean', 'path' => 'images/stickers/eat_clean.png', 'category' => 'Diet'],
            ['name' => 'Champion', 'path' => 'images/stickers/champion.png', 'category' => 'Motivation'],
        ];

        foreach ($stickers as $s) {
            CommunitySticker::create($s);
        }

        // Sample Posts
        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            CommunityPost::create([
                'user_id' => $admin->id,
                'content' => 'Bem-vindos à nova Comunidade NexShape! 🚀 Aqui vocês podem compartilhar seus treinos, dietas e conquistas. Vamos pra cima!',
                'status' => 'approved',
                'visibility' => 'public',
                'activity_status' => '🏆 Meta batida',
                'hashtags' => ['NexShape', 'Comunidade', 'Foco'],
            ]);
        }
    }
}
