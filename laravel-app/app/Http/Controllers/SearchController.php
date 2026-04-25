<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExerciseCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function search(Request $request): View
    {
        $query = trim($request->input('q'));
        
        // Regra de governança: não procurar palavras sem extensão (comprimento) mínima de 3 caracteres
        if (strlen($query) < 3) {
            $query = '';
        }

        $user = auth()->user();
        $isAdmin = $user->isAdministrator();

        $results = [];

        if (!empty($query)) {
            // 1. Catálogo de Exercícios
            $exercisesQuery = ExerciseCatalog::query();
            
            // Atletas só vêem ativos, Admins vêem tudo
            if (!$isAdmin) {
                $exercisesQuery->where('is_active', true);
            }

            $results['exercises'] = $exercisesQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('muscle_group', 'like', "%{$query}%")
                      ->orWhere('equipment', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();

            if ($isAdmin) {
                // 2. Utilizadores (Apenas Admin)
                $results['users'] = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%")
                    ->limit(10)
                    ->get();

                // 3. Logs de Erro (Apenas Admin)
                $results['errors'] = \App\Models\SystemError::where('message', 'like', "%{$query}%")
                    ->orWhere('url', 'like', "%{$query}%")
                    ->orWhere('type', 'like', "%{$query}%")
                    ->limit(10)
                    ->get();
            }
        }

        return view('search-results', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
