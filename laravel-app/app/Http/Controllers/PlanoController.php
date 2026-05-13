<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanoController extends Controller
{
    /**
     * Labels e ícones legíveis por tipo/role — usados na view para montar as tabs.
     */
    public const TAB_META = [
        'aluno'        => ['icon' => 'user-round',  'label' => 'Aluno'],
        'student'      => ['icon' => 'user-round',  'label' => 'Aluno'],
        'personal'     => ['icon' => 'stethoscope', 'label' => 'Profissional'],
        'nutricionista'=> ['icon' => 'salad',       'label' => 'Nutricionista'],
        'nutritionist' => ['icon' => 'salad',       'label' => 'Nutricionista'],
        'professional' => ['icon' => 'stethoscope', 'label' => 'Profissional'],
        'academia'     => ['icon' => 'building-2',  'label' => 'Clínica'],
        'clinic'       => ['icon' => 'building-2',  'label' => 'Clínica'],
        'full'         => ['icon' => 'layers',      'label' => 'Completo'],
    ];

    /**
     * Mapeamento de normalização para evitar confusão entre slugs técnicos e exibição.
     */
    public const GROUP_MAPPING = [
        'student'      => 'aluno',
        'clinic'       => 'academia',
        'manager'      => 'academia',
        'nutritionist' => 'nutricionista',
    ];

    public function __invoke(Request $request, MercadoPagoService $mp): View
    {
        $user       = $request->user();
        $mpFlash    = (string) session()->pull('flash_mp_error', '');

        // Carregar todos os planos ativos com roles e features em uma única query
        $allPlans = Plan::where('is_active', true)
            ->with(['planFeatures', 'roles'])
            ->get();

        // Função auxiliar para normalizar o agrupamento
        $normalizer = function (Plan $plan) {
            $firstRole = $plan->roles->first();
            $key = $firstRole ? $firstRole->name : $plan->type;
            return self::GROUP_MAPPING[$key] ?? $key;
        };

        // --- Regra 3: Administrador vê todos os planos (sem restrição) ---
        if ($user && $user->isAdministrator()) {
            $plans = $allPlans->groupBy($normalizer);

        // --- Regras 1 e 2: Usuário autenticado → filtrar pelas roles do usuário ---
        } elseif ($user) {
            $userRoleNames = $user->getRoleNames();

            $plans = $allPlans
                ->filter(fn(Plan $plan) => $plan->isAvailableForRoles($userRoleNames))
                ->groupBy($normalizer);

        // --- Regra 6: Visitante não autenticado → todos os planos, agrupados por perfil ---
        } else {
            $plans = $allPlans->groupBy($normalizer);
        }

        // --- Regra 5: Identificar o plano atual do usuário ---
        // Prioriza plano ativo via user_plans; fallback para plan_id direto
        $currentPlanId = null;
        if ($user) {
            $activePlan    = $user->activePlan;
            $currentPlanId = $activePlan ? $activePlan->plan_id : $user->plan_id;
        }

        return view('plano', [
            'plans'           => $plans,
            'currentPlanId'   => $currentPlanId,
            'tabMeta'         => self::TAB_META,
            'user'            => $user,
            'isPremium'       => $user ? $user->isPremiumActive() : false,
            'isAdministrator' => $user ? $user->isAdministrator() : false,
            'mpFlash'         => $mpFlash,
            'mpConfigured'    => config('projeto.mp_access_token') !== ''
                                 && rtrim((string) config('projeto.public_url'), '/') !== '',
            'webhookUrl'      => $mp->absoluteUrl('mp/webhook'),
            'pagamentoAtivo'  => \App\Models\AdminSetting::isTrue('pagamento_ativo', true),
        ]);
    }
}
