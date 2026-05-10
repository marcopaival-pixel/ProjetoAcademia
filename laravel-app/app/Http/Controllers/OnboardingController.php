<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WeightEntry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OnboardingController extends Controller
{
    /**
     * Tela 01: Welcome / Intro
     */
    public function welcome(): View
    {
        return view('onboarding.welcome');
    }

    /**
     * Tela 02: Name capture
     */
    public function step1(): View
    {
        return view('onboarding.step1');
    }

    public function saveStep1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:50',
        ]);

        Session::put('onboarding_data', array_merge(Session::get('onboarding_data', []), $validated));
        return redirect()->route('onboarding.step2');
    }

    /**
     * Tela 03: Goal selection
     */
    public function step2(): View
    {
        $data = Session::get('onboarding_data', []);
        return view('onboarding.step2', compact('data'));
    }

    public function saveStep2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'goal' => 'required|string',
        ]);

        $data = Session::get('onboarding_data', []);
        $data['goal'] = $validated['goal'];
        Session::put('onboarding_data', $data);

        return redirect()->route('onboarding.step3');
    }

    /**
     * Tela 04: Motivational Feedback
     */
    public function step2Feedback(): View
    {
        $data = Session::get('onboarding_data', []);
        return view('onboarding.step2_feedback', compact('data'));
    }

    /**
     * Tela 05: Obstacles
     */
    public function step2Obstacles(): View
    {
        return view('onboarding.step2_obstacles');
    }

    public function saveStep2Obstacles(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'obstacles' => 'required|array',
        ]);

        $data = Session::get('onboarding_data', []);
        $data['obstacles'] = $validated['obstacles'];
        Session::put('onboarding_data', $data);

        return redirect()->route('onboarding.step2.understanding');
    }

    /**
     * Tela 06: Understanding / Motivational
     */
    public function step2Understanding(): View
    {
        return view('onboarding.step2_understanding');
    }

    /**
     * Tela 07: Activity Level
     */
    public function step3(): View
    {
        return view('onboarding.step3');
    }

    public function saveStep3(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'activity_level' => 'required|string',
        ]);

        $data = Session::get('onboarding_data', []);
        $data['activity_level'] = $validated['activity_level'];
        Session::put('onboarding_data', $data);

        return redirect()->route('onboarding.step4');
    }

    /**
     * Tela 08: Personal Info (Sex, Birth, Country)
     */
    public function step4(): View
    {
        return view('onboarding.step4');
    }

    public function saveStep4(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gender' => 'required|string',
            'birth_date' => 'required|date',
            'country' => 'required|string',
        ]);

        $data = Session::get('onboarding_data', []);
        $data = array_merge($data, $validated);
        Session::put('onboarding_data', $data);

        return redirect()->route('onboarding.step5');
    }

    /**
     * New Screen between 08 and 09: Specs (Height, Weight, target weight)
     */
    public function step5(): View
    {
        return view('onboarding.step5');
    }

    public function saveStep5(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'target_weight' => 'required|numeric',
        ]);

        $data = Session::get('onboarding_data', []);
        $data = array_merge($data, $validated);
        Session::put('onboarding_data', $data);

        return redirect()->route('onboarding.step6');
    }

    /**
     * Tela 09: Meta semanal
     */
    public function step6(): View
    {
        return view('onboarding.step6');
    }

    public function saveStep6(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'weekly_goal' => 'required|string',
        ]);

        $data = Session::get('onboarding_data', []);
        $data['weekly_goal'] = $validated['weekly_goal'];
        Session::put('onboarding_data', $data);

        return redirect()->route('onboarding.step7');
    }

    /**
     * Tela 10: Account Creation
     */
    public function step7(): View
    {
        return view('onboarding.step7');
    }

    public function saveStep7(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $data = Session::get('onboarding_data', []);
        $data['email'] = $request->email;
        $data['password'] = $request->password;
        Session::put('onboarding_data', $data);

        return redirect()->route('onboarding.step8');
    }

    /**
     * Tela 11: Username
     */
    public function step8(): View
    {
        return view('onboarding.step8');
    }

    public function saveStep8(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|min:3',
        ]);

        $data = Session::get('onboarding_data', []);
        $data['username'] = $request->username;
        Session::put('onboarding_data', $data);

        // Criar o usuário e perfil real no banco
        return DB::transaction(function () use ($data) {
            $user = new User([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'is_premium' => false,
            ]);
            $user->password_hash = Hash::make($data['password']);
            $user->save();

            // Calcular TMB e Meta Calórica usando o Serviço Centralizado (Máxima Eficácia)
            $weight = (float)$data['weight'];
            $height = (float)$data['height'];
            $birthDate = $data['birth_date'];
            $isMale = $data['gender'] === 'Masculino';
            $sex = $isMale ? 'M' : 'F';
            
            // Mapear níveis de atividade do onboarding para os slugs do serviço
            $activitySlug = match ($data['activity_level']) {
                'Não muito ativo' => 'sedentary',
                'Levemente ativo' => 'light',
                'Ativo' => 'moderate',
                'Bastante ativo' => 'active',
                default => 'sedentary',
            };

            // Mapear objetivos do onboarding para os slugs do serviço (Mais abrangente)
            $goalSlug = 'maintain';
            $goalText = $data['goal'] ?? '';
            
            if (str_contains($goalText, 'Agressivo')) {
                $goalSlug = 'lose_aggressive';
            } elseif (str_contains($goalText, 'Perder') || str_contains($goalText, 'Emagrecer')) {
                $goalSlug = 'lose';
            } elseif (str_contains($goalText, 'Ganhar') || str_contains($goalText, 'Massa') || str_contains($goalText, 'Hipertrofia')) {
                $goalSlug = 'gain';
            } elseif (str_contains($goalText, 'Recomposição')) {
                $goalSlug = 'recomp';
            } elseif (str_contains($goalText, 'Performance')) {
                $goalSlug = 'performance';
            }

            // Usar o serviço para estimativa precisa
            $estimation = \App\Services\Nutrition::estimateTarget(
                $birthDate,
                (int)$height,
                $sex,
                $activitySlug,
                $goalSlug,
                $weight
            );

            $calorieTarget = $estimation['ok'] ? $estimation['target'] : 2000;
            
            // Cálculo Automático de Água (Novo)
            $waterTarget = \App\Services\Nutrition::calculateWaterTarget(
                $weight,
                $birthDate,
                $sex,
                $activitySlug,
                'moderate' // Padrão moderado para clima no onboarding
            );

            UserProfile::create([
                'user_id' => $user->id,
                'birth_date' => $birthDate,
                'sex' => $sex,
                'height_cm' => (int)$height,
                'activity_level' => $activitySlug,
                'goal' => $goalSlug,
                'daily_calorie_target' => $calorieTarget,
                'water_target_ml' => $waterTarget,
                'is_water_target_auto' => true,
                'target_weight_kg' => (float)$data['target_weight'],
            ]);

            // Registrar Consentimento Inicial (LGPD)
            \App\Models\UserConsent::create([
                'user_id' => $user->id,
                'consent_type' => 'privacy_policy_and_terms',
                'version' => '1.0',
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);

            // Registrar peso inicial
            WeightEntry::create([
                'user_id' => $user->id,
                'weighed_at' => now()->toDateString(),
                'weight_kg' => $weight,
            ]);

            $user->onboarding_status = 'completed';
            $user->profile_completion_percentage = 100;
            $user->save();

            Auth::login($user);

            return redirect()->route('onboarding.finish');
        });
    }

    /**
     * Tela 12: Summary / Congrats
     */
    public function finish(): View
    {
        $user = Auth::user();
        $data = Session::get('onboarding_data', []);
        
        // Se já temos o usuário logado e perfil criado, priorizar os dados do banco
        if ($user && $user->profile) {
            $data['daily_calorie_target'] = $user->profile->daily_calorie_target;
            $data['target_weight'] = $user->profile->target_weight_kg;
            $user->updateProfileCompletion();
        }

        // Limpar dados temporários do onboarding para evitar re-uso acidental
        Session::forget('onboarding_data');

        return view('onboarding.finish', compact('data'));
    }

    /**
     * Atualiza o perfil do usuário durante o onboarding (Modal AJAX).
     */
    public function update(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Onboarding Update Attempt', ['user_id' => auth()->id(), 'data' => $request->all()]);
        
        $user = Auth::user();
        
        // Sanitizar inputs: converter strings vazias em null para evitar falhas de validação
        $data = collect($request->all())->map(function($value) {
            return $value === "" ? null : $value;
        })->toArray();
        $request->merge($data);

        try {
            $validated = $request->validate([
                'birth_date' => 'nullable|date',
                'sex' => 'nullable|string|in:M,F',
                'height_cm' => 'nullable|integer|min:50|max:250',
                'weight_kg' => 'nullable|numeric|min:20|max:500',
                'activity_level' => 'nullable|string',
                'goal' => 'nullable|string|in:lose,lose_aggressive,recomp,maintain,gain,performance',
                'target_weight_kg' => 'nullable|numeric|min:20|max:500',
                'training_days_per_week' => 'nullable|integer|min:0|max:7',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning('Onboarding Validation Failed', ['user_id' => auth()->id(), 'errors' => $e->errors()]);
            throw $e;
        }

        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        $profile->fill($request->only([
            'birth_date', 'sex', 'height_cm', 'activity_level', 'goal', 
            'target_weight_kg', 'training_days_per_week'
        ]));
        
        $profile->save();

        if ($request->has('weight_kg') && !empty($request->weight_kg)) {
            WeightEntry::updateOrCreate(
                ['user_id' => $user->id, 'weighed_at' => now()->toDateString()],
                ['weight_kg' => $request->weight_kg]
            );
        }

        // Importante: Limpar o cache da relação para que o updateProfileCompletion pegue os dados salvos
        $user->unsetRelation('profile');
        $percentage = $user->updateProfileCompletion();

        return response()->json([
            'success' => true,
            'percentage' => $percentage,
            'status' => $user->onboarding_status,
            'message' => 'Perfil atualizado com sucesso!'
        ]);
    }

    /**
     * Pula o onboarding por enquanto.
     */
    public function skip()
    {
        $user = Auth::user();
        $user->onboarding_status = 'skipped';
        $user->save();

        session()->forget('show_onboarding_modal');

        return response()->json([
            'success' => true,
            'message' => 'Onboarding pulado.'
        ]);
    }

    /**
     * Marca o onboarding como concluído.
     */
    public function complete()
    {
        $user = Auth::user();
        $user->onboarding_status = 'completed';
        $user->profile_completion_percentage = 100;
        $user->save();

        session()->forget('show_onboarding_modal');

        return response()->json([
            'success' => true,
            'message' => 'Onboarding concluído!'
        ]);
    }
}


