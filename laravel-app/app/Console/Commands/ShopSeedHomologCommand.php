<?php

namespace App\Console\Commands;

use Database\Seeders\ShopHomologSeeder;
use Illuminate\Console\Command;

class ShopSeedHomologCommand extends Command
{
    protected $signature = 'app:shop:seed-homolog';

    protected $description = 'Popula catálogo demo do shopping (produtos, cupons, settings) para homologação local.';

    public function handle(): int
    {
        $this->info('A executar ShopHomologSeeder...');
        app(ShopHomologSeeder::class)->run();

        return self::SUCCESS;
    }
}
