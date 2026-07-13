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
        $isUppercase = (ucfirst($term) === $term);
        $activeRole = session('active_role');

        if (in_array($termLower, ['paciente', 'aluno', 'pacientes', 'alunos'])) {
            if ($activeRole === 'aluno') {
                $result = in_array($termLower, ['paciente', 'aluno']) ? 'aluno assistido' : 'alunos assistidos';
                return $isUppercase ? mb_convert_case($result, MB_CASE_TITLE, "UTF-8") : $result;
            }

            // Se não houver usuário ou se não for profissional, retorna o padrão
            if (!$user || !$user->hasRole('professional')) {
                $result = in_array($termLower, ['paciente', 'aluno']) ? 'paciente' : 'pacientes';
                return $isUppercase ? mb_convert_case($result, MB_CASE_TITLE, "UTF-8") : $result;
            }

            // Obtém a especialidade do contexto da sessão (para profissionais com múltiplas)
            $activeSpecialtyId = session('active_specialty_id');
            $specialty = null;

            if ($activeSpecialtyId) {
                $specialty = \App\Models\Especialidade::find($activeSpecialtyId);
            }

            // Se não tem na sessão, pega a principal do profile
            if (!$specialty && $user->professionalProfile) {
                /** @var \App\Models\ProfessionalProfile|null $profile */
                $profile = $user->professionalProfile;
                $specialty = $profile ? $profile->especialidade : null;
            }

            if ($specialty instanceof \App\Models\Especialidade && $specialty->client_term) {
                $result = strtolower($specialty->client_term);
                if (in_array($termLower, ['pacientes', 'alunos'])) {
                    if (!str_ends_with($result, 's')) {
                        $result .= 's';
                    }
                }
            } else {
                $result = in_array($termLower, ['paciente', 'aluno']) ? 'paciente' : 'pacientes';
            }

            return $isUppercase ? mb_convert_case($result, MB_CASE_TITLE, "UTF-8") : $result;
        }

        // Se for string com a primeira maiúscula, mantém
        if ($isUppercase) {
            return ucfirst($termLower);
        }

        return $term;
    }
}
