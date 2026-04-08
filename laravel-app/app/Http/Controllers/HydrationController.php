<?php

namespace App\Http\Controllers;

use App\Models\WaterEntry;
use App\Services\Nutrition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HydrationController extends Controller
{
    /**
     * Retorna o status atual de hidratação do usuário autenticado.
     */
    public function status()
    {
        $user = Auth::user();
        $profile = $user->profile ?: $user->profile()->create();
        
        $today = Carbon::today();
        
        $entries = $user->waterEntries()
            ->whereDate('entry_date', $today)
            ->orderBy('drank_at', 'desc')
            ->get();
            
        $totalToday = $entries->sum('amount_ml');
        $target = $profile->water_target_ml ?? 2000;
        
        return response()->json([
            'target' => $target,
            'consumed' => $totalToday,
            'percentage' => $target > 0 ? round(($totalToday / $target) * 100) : 0,
            'entries' => $entries,
            'is_auto' => (bool) $profile->is_water_target_auto,
        ]);
    }

    /**
     * Registra um novo consumo de água.
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'amount_ml' => 'required|integer|min:1|max:5000',
                'source' => 'nullable|string|max:20',
            ]);

            $user = Auth::user();
            
            $entry = $user->waterEntries()->create([
                'entry_date' => Carbon::today(),
                'drank_at' => Carbon::now(),
                'amount_ml' => $request->amount_ml,
                'source' => $request->source ?? 'manual',
            ]);

            return response()->json([
                'success' => true,
                'entry' => $entry,
                'message' => 'Hidratação registrada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro sistêmico ao registrar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove um registro de água.
     */
    public function destroy(WaterEntry $entry)
    {
        if ($entry->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $entry->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Atualiza as configurações de hidratação.
     */
    public function updateSettings(Request $request)
    {
        try {
            $request->validate([
                'water_target_ml' => 'nullable|integer|min:500',
                'is_water_target_auto' => 'nullable|boolean',
                'climate' => 'nullable|string|in:cold,moderate,hot',
            ]);

            $user = Auth::user();
            $profile = $user->profile ?: $user->profile()->create();

            if ($request->has('is_water_target_auto')) {
                $profile->is_water_target_auto = $request->is_water_target_auto;
            }

            if ($request->has('climate')) {
                $profile->climate = $request->climate;
            }

            if ($profile->is_water_target_auto) {
                // Verificar se temos dados básicos para o cálculo
                $weight = $user->weightEntries()->latest('weighed_at')->value('weight_kg');
                
                if (!$weight) {
                    return response()->json([
                        'success' => false,
                        'message' => 'É necessário registar o seu Peso atual para usar o Modo Inteligente.'
                    ], 422);
                }

                if (!$profile->birth_date || !$profile->sex) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Por favor, complete a Data de Nascimento e Sexo no seu Perfil para calibrar o sistema.'
                    ], 422);
                }

                // Recalcula se estiver no modo auto
                $profile->water_target_ml = Nutrition::calculateWaterTarget(
                    $weight,
                    $profile->birth_date?->format('Y-m-d'),
                    $profile->sex ?? 'M',
                    $profile->activity_level ?? 'moderate',
                    $profile->climate ?? 'moderate'
                );
            } elseif ($request->has('water_target_ml')) {
                $profile->water_target_ml = $request->water_target_ml;
            }

            $profile->save();

            return response()->json([
                'success' => true,
                'target' => $profile->water_target_ml,
                'is_auto' => $profile->is_water_target_auto
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha na calibração bio-métrica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados para relatórios.
     */
    public function reports(Request $request)
    {
        $days = $request->get('days', 7);
        $user = Auth::user();
        
        $data = $user->waterEntries()
            ->where('entry_date', '>=', Carbon::today()->subDays($days - 1))
            ->selectRaw('entry_date, SUM(amount_ml) as total')
            ->groupBy('entry_date')
            ->orderBy('entry_date', 'asc')
            ->get();

        return response()->json($data);
    }
}
