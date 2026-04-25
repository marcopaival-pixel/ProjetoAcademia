<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPatientStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-patient-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ativa todos os vínculos de pacientes que estavam marcados como Não';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = DB::table('pacientes')->where('status', 'Não')->update(['status' => 'Sim']);
        $this->info("Sucesso: {$count} vínculos foram ativados.");
    }
}
