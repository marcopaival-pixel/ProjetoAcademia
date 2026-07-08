<?php

namespace App\Console\Commands;

use App\Models\AcademyCompany;
use App\Services\TenantHealthService;
use Illuminate\Console\Command;

class CalculateTenantHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:calculate-health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula o Health Score e Risco de Churn de todas as academias ativas';

    /**
     * Execute the console command.
     */
    public function handle(TenantHealthService $healthService)
    {
        $this->info('Iniciando cálculo de Health Score...');

        $companies = AcademyCompany::where('is_active', true)->get();
        $bar = $this->output->createProgressBar(count($companies));

        $bar->start();

        foreach ($companies as $company) {
            $healthService->updateHealthScore($company);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Cálculo finalizado com sucesso.');
    }
}
