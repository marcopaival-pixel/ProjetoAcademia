<?php

namespace App\Http\Controllers;

use App\Services\DemoDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    protected $demoService;

    public function __construct(DemoDataService $demoService)
    {
        $this->demoService = $demoService;
    }

    public function start(Request $request)
    {
        $profile = $request->input('profile', 'professional');
        
        // Setup do ambiente
        $demoUser = $this->demoService->setupDemoEnvironment($profile);

        // Autenticar como usuário demo
        Auth::login($demoUser);

        // Ativar flag de sessão
        session([
            'is_demo_mode' => true,
            'demo_profile' => $profile,
            'active_role' => $profile
        ]);

        if (in_array($profile, ['clinic', 'gestor', 'admin'])) {
            return redirect()->route('home')->with('error', 'O painel administrativo não está disponível no modo demonstração.');
        }

        if ($profile === 'professional') {
            return redirect()->route('professional.dashboard')->with('success', 'Modo Demonstração Ativado! Visão Profissional');
        }

        return redirect()->route('dashboard')->with('success', 'Modo Demonstração Ativado! Explore o sistema livremente.');
    }

    public function stop()
    {
        // Se estiver logado como demo, deslogar
        if (Auth::user()?->is_demo) {
            Auth::logout();
        }

        session()->forget(['is_demo_mode', 'demo_profile']);

        return redirect()->route('home')->with('info', 'Você saiu do Modo Demonstração.');
    }

    public function reset()
    {
        if (!session('is_demo_mode')) {
            return redirect()->back();
        }

        $user = Auth::user();
        if ($user && $user->is_demo) {
            $this->demoService->clearDemoData($user);
            $this->demoService->setupDemoEnvironment(session('demo_profile', 'professional'));
        }

        return redirect()->back()->with('success', 'Ambiente de demonstração resetado com sucesso!');
    }

    public function switchProfile(Request $request)
    {
        $profile = $request->input('profile');
        if (!in_array($profile, ['aluno', 'professional'])) {
            return redirect()->back()->with('error', 'Perfil inválido ou não disponível na demonstração.');
        }

        session(['demo_profile' => $profile, 'active_role' => $profile]);

        $user = Auth::user();
        if ($user && $user->is_demo) {
            $this->demoService->setupDemoEnvironment($profile, $user);
            
            // Forçar atualização do usuário na sessão para refletir novo is_admin
            $user->refresh();
            Auth::login($user);

            // Redirecionamento específico por perfil
            if (in_array($profile, ['clinic', 'gestor', 'admin'])) {
                return redirect()->back()->with('error', 'Painel administrativo desabilitado.');
            }

            if ($profile === 'professional') {
                return redirect()->route('professional.dashboard')->with('success', 'Visão Profissional Ativada');
            }

            return redirect()->route('dashboard')->with('success', 'Visão Aluno Ativada');
        }
    }
}
