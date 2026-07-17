<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize-images {--quality=80 : Qualidade da imagem WebP (0-100)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converte e otimiza imagens PNG/JPG do diretório public/images para WebP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $quality = (int) $this->option('quality');
        $directory = public_path('images');

        if (!File::exists($directory)) {
            $this->error("Diretório $directory não encontrado.");
            return 1;
        }

        $files = File::allFiles($directory);
        $count = 0;

        $this->info("Iniciando otimização de imagens em: $directory");

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            
            if (!in_array($extension, ['png', 'jpg', 'jpeg'])) {
                continue;
            }

            $sourcePath = $file->getRealPath();
            $destinationPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $sourcePath);

            if (File::exists($destinationPath)) {
                $this->line("Pulando: {$file->getFilename()} (já existe .webp)");
                continue;
            }

            $this->comment("Processando: {$file->getFilename()}...");

            try {
                if ($extension === 'png') {
                    $image = imagecreatefrompng($sourcePath);
                } else {
                    $image = imagecreatefromjpeg($sourcePath);
                }

                if (!$image) {
                    throw new \Exception("Não foi possível carregar a imagem.");
                }

                // Preservar transparência se for PNG
                if ($extension === 'png') {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }

                if (imagewebp($image, $destinationPath, $quality)) {
                    $oldSize = round($file->getSize() / 1024, 2);
                    $newSize = round(File::size($destinationPath) / 1024, 2);
                    $reduction = round((($oldSize - $newSize) / $oldSize) * 100, 1);

                    $this->info("Sucesso: {$file->getFilename()} -> WebP ({$newSize}KB | -{$reduction}%)");
                    imagedestroy($image);
                    $count++;
                } else {
                    $this->error("Erro ao converter: {$file->getFilename()}");
                }
            } catch (\Throwable $e) {
                $this->error("Erro no arquivo {$file->getFilename()}: " . $e->getMessage());
            }
        }

        $this->info("\nOtimização concluída! $count imagens convertidas para WebP.");
        $this->warn("Dica: Atualize suas views para usar a extensão .webp ou implemente uma lógica de fallback.");

        return 0;
    }
}
