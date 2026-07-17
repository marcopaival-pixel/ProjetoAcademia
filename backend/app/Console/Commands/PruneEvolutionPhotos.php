<?php

namespace App\Console\Commands;

use App\Models\EvolutionPhoto;
use App\Models\EvolutionSessionAnalysis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneEvolutionPhotos extends Command
{
    protected $signature = 'evolution:prune-photos
        {--days=730 : Quantidade de dias de fotos a manter}
        {--force : Executar sem confirmação}';

    protected $description = 'Remove fotos corporais antigas e análises de sessão vinculadas após o prazo de retenção.';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $force = (bool) $this->option('force');

        if (! $force && ! $this->confirm("Remover fotos de evolução com mais de {$days} dias?")) {
            $this->info('Operação cancelada.');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days)->toDateString();
        $deletedPhotos = 0;

        EvolutionPhoto::query()
            ->whereDate('registered_date', '<', $cutoff)
            ->orderBy('id')
            ->chunkById(100, function ($photos) use (&$deletedPhotos) {
                foreach ($photos as $photo) {
                    if ($photo->photo_path) {
                        Storage::disk('public')->delete($photo->photo_path);
                    }

                    EvolutionSessionAnalysis::query()
                        ->where('user_id', $photo->user_id)
                        ->whereDate('session_date', $photo->registered_date)
                        ->delete();

                    $photo->delete();
                    $deletedPhotos++;
                }
            });

        $this->info("Fotos de evolução removidas: {$deletedPhotos}");

        return self::SUCCESS;
    }
}
