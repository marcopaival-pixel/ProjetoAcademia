<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\HealthAlert;
use App\Models\ExerciseEntry;
use App\Models\WeightEntry;
use Carbon\Carbon;

class NexBotCoachCommand extends Command
{
    protected $signature = 'nexbot:coach';
    protected $description = 'Analiza o comportamento dos alunos e gera alertas de saúde e motivação.';

    public function handle()
    {
        $this->info('Iniciando análise do NexBot Coach...');

        $users = User::where('status', 'active')->get();

        foreach ($users as $user) {
            $this->analyzeUser($user);
        }

        $this->info('Análise concluída!');
    }

    private function analyzeUser(User $user)
    {
        // 1. Inatividade (Sem treino há mais de 5 dias)
        $lastWorkout = ExerciseEntry::where('user_id', $user->id)->latest('entry_date')->first();
        if ($lastWorkout && Carbon::parse($lastWorkout->entry_date)->diffInDays(now()) >= 5) {
            $this->createAlert($user, 'inactivity', 'danger', "NexBot detectou inatividade! Você não treina há " . Carbon::parse($lastWorkout->entry_date)->diffInDays(now()) . " dias. Vamos voltar ao ritmo?");
        }

        // 2. Sem evolução de peso (Mesmo peso há mais de 30 dias com objetivo de ganho/perda)
        $profile = $user->profile;
        if ($profile && in_array($profile->goal, ['lose', 'lose_aggressive', 'gain'])) {
            $weights = WeightEntry::where('user_id', $user->id)
                ->where('weighed_at', '>=', now()->subDays(30))
                ->orderBy('weighed_at', 'asc')
                ->get();

            if ($weights->count() >= 2) {
                $first = $weights->first()->weight_kg;
                $last = $weights->last()->weight_kg;
                if (abs($first - $last) < 0.2) {
                    $this->createAlert($user, 'no_evolution', 'warning', "Parece que você atingiu um platô. Que tal ajustar sua meta calórica ou intensidade de treino?");
                }
            }
        }

        // 3. Baixa frequência de registros
        $waterLogs = $user->waterEntries()->where('entry_date', '>=', now()->subDays(7))->count();
        if ($waterLogs < 3) {
            $this->createAlert($user, 'low_engagement', 'info', "Não esqueça de registrar seu consumo de água. Isso ajuda a IA a monitorar sua saúde real.");
        }
    }

    private function createAlert(User $user, string $type, string $severity, string $message)
    {
        // Evita duplicar o mesmo alerta no mesmo dia
        $exists = HealthAlert::where('user_id', $user->id)
            ->where('type', $type)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$exists) {
            HealthAlert::create([
                'user_id' => $user->id,
                'type' => $type,
                'severity' => $severity,
                'message' => $message
            ]);
        }
    }
}
