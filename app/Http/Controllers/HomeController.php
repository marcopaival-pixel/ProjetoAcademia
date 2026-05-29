<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(\Illuminate\Http\Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        
        // Obter um plano de cada tipo para o resumo da home
        $query = \App\Models\Plan::where('is_active', true)
            ->with('planFeatures');

        // Se o usuário estiver logado e tiver um perfil definido, podemos priorizar o tipo dele
        // Mas o pedido pede 3 categorias: Aluno, Profissional, Clínica.
        // Vamos pegar o plano 'mais popular' ou o primeiro de cada tipo.
        
        $summaryPlans = [
            'student' => \App\Models\Plan::where('is_active', true)->where('type', 'student')->orderBy('price', 'asc')->first(),
            'professional' => \App\Models\Plan::where('is_active', true)->where('type', 'professional')->orderBy('price', 'asc')->skip(1)->first() ?? \App\Models\Plan::where('is_active', true)->where('type', 'professional')->first(),
            'clinic' => \App\Models\Plan::where('is_active', true)->where('type', 'clinic')->orderBy('price', 'asc')->first(),
        ];

        // Se o usuário já escolheu um tipo no onboarding ou perfil, podemos filtrar
        $preferredType = null;
        if ($user) {
            // Lógica para detectar tipo preferido (ex: baseado em roles ou última escolha)
            if ($user->hasRole('patient')) $preferredType = 'student';
            elseif ($user->hasRole('professional')) $preferredType = 'professional';
            elseif ($user->hasRole('manager')) $preferredType = 'clinic';
        }

        $communityPosts = \App\Models\CommunityPost::with(['user', 'reactions', 'comments', 'media'])
            ->where('status', 'approved')
            ->where('visibility', 'public')
            ->latest()
            ->take(5)
            ->get();

        return view('home', [
            'summaryPlans' => $summaryPlans,
            'preferredType' => $preferredType,
            'user' => $user,
            'communityPosts' => $communityPosts
        ]);
    }
}
