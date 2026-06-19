<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfessionalPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalPatientSelectorController extends Controller
{
    /**
     * Retorna os dados para o Seletor Global de Pacientes/Alunos.
     */
    public function index(Request $request)
    {
        $professional = auth()->user();
        
        $query = $professional->patients()
            ->wherePivotIn('status', ['Sim', 'PENDENTE'])
            ->with(['profile', 'weightEntries' => function($q) { $q->orderBy('weighed_at', 'desc')->limit(1); }])
            ->withCount('foodEntries');

        // Busca em tempo real
        $search = $request->get('q');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.cpf', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%")
                  ->orWhere('users.id', $search);
            });
        }

        // Filtro Rápido
        $filter = $request->get('filter', 'todos');
        if ($filter !== 'todos') {
            if ($filter === 'ativos') {
                $query->where('users.status', 'active');
            } elseif ($filter === 'inativos') {
                $query->where('users.status', 'inactive');
            } elseif ($filter === 'arquivados') {
                $query->wherePivot('status', 'Não');
            } elseif ($filter === 'aniversariantes') {
                $currentMonth = now()->month;
                $query->whereHas('profile', function($q) use ($currentMonth) {
                    $q->whereMonth('birth_date', $currentMonth);
                });
            }
            // Outros filtros complexos como 'Treino Ativo', 'Consulta Hoje' podem ser implementados via whereHas com as devidas relações.
        }

        $patients = $query->get();

        // Estruturar dados
        $favorites = [];
        $recent = [];
        $alphabetical = [];

        foreach ($patients as $patient) {
            $pivot = $patient->pivot;
            $data = $this->formatPatientData($patient, $pivot);

            if ($pivot->is_favorite) {
                $favorites[] = $data;
            }

            if ($pivot->last_accessed_at) {
                $recent[] = $data;
            }

            // Agrupamento Alfabético
            $firstLetter = strtoupper(mb_substr($patient->name, 0, 1));
            if (!preg_match('/^[A-Z]$/', $firstLetter)) {
                $firstLetter = '#';
            }
            if (!isset($alphabetical[$firstLetter])) {
                $alphabetical[$firstLetter] = [];
            }
            $alphabetical[$firstLetter][] = $data;
        }

        // Ordenar os recentes (mais novos primeiro) - limite de 5
        usort($recent, fn($a, $b) => strtotime($b['last_accessed_at']) - strtotime($a['last_accessed_at']));
        $recent = array_slice($recent, 0, 5);

        // Ordenar chaves alfabéticas
        ksort($alphabetical);
        foreach ($alphabetical as &$group) {
            usort($group, fn($a, $b) => strcmp($a['name'], $b['name']));
        }

        return response()->json([
            'favorites' => $favorites,
            'recent' => $recent,
            'alphabetical' => $alphabetical,
        ]);
    }

    /**
     * Alterna o status de favorito de um paciente para o profissional atual.
     */
    public function toggleFavorite(Request $request, User $patient)
    {
        $professional = auth()->user();

        $pivot = ProfessionalPatient::where('profissional_id', $professional->id)
            ->where('user_id', $patient->id)
            ->first();

        if (!$pivot) {
            return response()->json(['error' => 'Paciente não encontrado.'], 404);
        }

        $pivot->is_favorite = !$pivot->is_favorite;
        $pivot->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $pivot->is_favorite
        ]);
    }

    /**
     * Helper para formatar os dados resumidos do paciente para a lista.
     */
    private function formatPatientData($patient, $pivot)
    {
        $status = 'Inativo';
        if ($pivot->status === 'Sim') {
            $status = $patient->status === 'active' ? 'Ativo' : 'Pendente';
        }

        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'photo_url' => $patient->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($patient->name).'&color=10b981&background=09090b&bold=true',
            'status' => $status,
            'is_favorite' => (bool)$pivot->is_favorite,
            'last_accessed_at' => $pivot->last_accessed_at,
            // Valores mockados / simplificados para os requisitos de visualização rápida. 
            // Em produção devem buscar relações (ex: ultima_avaliacao -> patient->assessments()->latest())
            'last_assessment' => '--',
            'next_consultation' => '--',
            'active_training' => '--',
            'financial_indicator' => 'Sem pendências',
            'last_portal_access' => $patient->last_activity_at ? $patient->last_activity_at->format('d/m/Y H:i') : '--',
        ];
    }
}
