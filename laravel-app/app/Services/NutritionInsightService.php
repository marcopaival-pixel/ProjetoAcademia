<?php

namespace App\Services;

use App\Models\FoodEntry;
use App\Models\User;
use App\Models\UserProfile;

class NutritionInsightService
{
    /**
     * Auditoria semanal determinística (sem LLM).
     */
    public function weeklyAudit(User $user): array
    {
        $profile = UserProfile::where('user_id', $user->id)->first();
        $goal = $profile->goal ?? 'maintain';
        $targetKcal = (int) ($profile->daily_calorie_target ?? 2000);

        $history = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(7)->format('Y-m-d'))
            ->selectRaw('entry_date, SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->groupBy('entry_date')
            ->orderBy('entry_date', 'desc')
            ->get();

        if ($history->isEmpty()) {
            return [
                'ok' => false,
                'error' => 'Você precisa registrar pelo menos alguns dias de alimentação para uma auditoria.',
            ];
        }

        $daysLogged = $history->count();
        $avgKcal = (int) round($history->avg('cal'));
        $avgP = round($history->avg('p'), 1);
        $avgC = round($history->avg('c'), 1);
        $avgF = round($history->avg('f'), 1);
        $targetDays = 7;
        $adherencePct = (int) round(($daysLogged / $targetDays) * 100);

        $lines = ["**Auditoria nutricional — últimos 7 dias**\n"];
        $lines[] = "- Dias com registro: **{$daysLogged}/7** ({$adherencePct}% de aderência)";
        $lines[] = "- Média diária: **{$avgKcal} kcal** (meta: {$targetKcal} kcal)";
        $lines[] = "- Macros médios: P **{$avgP}g** · C **{$avgC}g** · G **{$avgF}g**";

        if ($adherencePct >= 85) {
            $lines[] = "\n**Consistência excelente.** Manter o ritmo de registro acelera ajustes finos.";
        } elseif ($adherencePct >= 50) {
            $lines[] = "\n**Boa base**, mas registre mais dias para análise precisa.";
        } else {
            $lines[] = "\n**Prioridade:** registrar pelo menos 1 refeição por dia.";
        }

        $kcalDelta = $avgKcal - $targetKcal;
        if ($goal === 'lose_weight' || $goal === 'emagrecimento') {
            if ($kcalDelta > 150) {
                $lines[] = 'Para emagrecimento, a média está acima da meta — reduza porções ou refeições extras.';
            } elseif ($kcalDelta < -400) {
                $lines[] = 'Déficit muito agressivo — risco de perda muscular; considere aumentar proteínas.';
            } else {
                $lines[] = 'Calorias alinhadas ao objetivo de emagrecimento.';
            }
        } elseif ($goal === 'gain_mass' || $goal === 'hipertrofia') {
            if ($kcalDelta < -200) {
                $lines[] = 'Para hipertrofia, aumente calorias e proteínas (mín. 1,6 g/kg).';
            } else {
                $lines[] = 'Ingestão compatível com ganho de massa.';
            }
        }

        $lowProteinDays = $history->filter(fn ($d) => ($d->p ?? 0) < ($avgP * 0.6))->count();
        if ($lowProteinDays >= 3) {
            $lines[] = 'Vários dias com proteína baixa — inclua fontes magras em cada refeição principal.';
        }

        return [
            'ok' => true,
            'message' => implode("\n", $lines),
            'tokens' => 0,
            'model' => 'rule_engine',
        ];
    }
}
