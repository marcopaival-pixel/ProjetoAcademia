<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('__t')) {
    /**
     * Terminology Helper
     * Retorna a tradução adequada baseada na especialidade ativa do profissional.
     * 
     * Exemplo: __t('paciente') -> 'Aluno' (se Educador Físico) ou 'Paciente' (se Nutricionista).
     */
    function __t(string $term): string
    {
        $termLower = strtolower($term);
        $user = Auth::user();
        
        // Se não houver usuário ou se não for profissional, retorna o padrão
        if (!$user || !$user->hasRole('professional')) {
            if ($termLower === 'paciente' || $termLower === 'aluno') {
                // Tenta pegar o termo da clínica caso exista, mas o default seguro é Paciente
                return 'Paciente';
            }
            return ucfirst($term);
        }

        // Obtém a especialidade do contexto da sessão (para profissionais com múltiplas)
        $activeSpecialtyId = session('active_specialty_id');
        $specialty = null;

        if ($activeSpecialtyId) {
            $specialty = \App\Models\Especialidade::find($activeSpecialtyId);
        }

        // Se não tem na sessão, pega a principal do profile
        if (!$specialty && $user->professionalProfile) {
            $specialty = $user->professionalProfile->especialidade;
        }

        if ($termLower === 'paciente' || $termLower === 'aluno') {
            return $specialty && $specialty->client_term ? $specialty->client_term : 'Paciente';
        }

        // Adicionar outros mapeamentos aqui (ex: 'prontuário' vs 'anamnese')
        
        // Se for string com a primeira maiúscula, mantém
        if (ucfirst($term) === $term) {
            return ucfirst($termLower);
        }

        return $term;
    }
}
