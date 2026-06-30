<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Lista todos os pacientes vinculados ao profissional.
     */
    public function index(Request $request): View
    {
        $professional = auth()->user();
        
        // Parâmetros de Filtro e Paginação
        $search = $request->get('search');
        $status = $request->get('status');
        $goal = $request->get('goal');
        $perPage = 10; // Fixo em 10 conforme requisito
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        // Mapeamento de colunas para ordenação
        $sortMap = [
            'name' => 'name',
            'last_activity' => 'last_activity_at',
            'created_at' => 'pacientes.created_at',
        ];

        $orderBy = $sortMap[$sort] ?? 'name';

        // Requirement: "listagem deve exibir somente usuários que possuam o perfil de Paciente"
        $query = $professional->patients()
            ->whereHas('roles', function($q) {
                $q->where('name', 'paciente');
            })
            ->with([
                'profile', 
                'roles',
                'weightEntries' => function($q) { $q->orderBy('weighed_at', 'desc')->limit(2); },
                'assessments'   => function($q) { $q->whereNotNull('bf_percent')->orderBy('assessment_date', 'desc')->limit(2); },
            ])
            ->withCount(['foodEntries' => function($q) { 
                $q->where('entry_date', '>=', now()->subDays(7)); 
            }]);

        // Busca por Nome ou Email
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // Filtro por Objetivo
        if ($goal) {
            $query->whereHas('profile', function($q) use ($goal) {
                $q->where('goal', $goal);
            });
        }

        // Filtro por Status de Vínculo
        if ($status) {
            if ($status === 'Inativo') {
                $query->where('pacientes.status', 'Não');
            } elseif ($status === 'Ativo') {
                $query->where('users.status', 'active')->where('pacientes.status', 'Sim');
            } elseif ($status === 'Pendente') {
                $query->where('users.status', 'pending');
            }
        } else {
            // Por padrão, mostramos apenas os ativos e pendentes para evitar poluição
            $query->where('pacientes.status', 'Sim');
        }

        // Ordenação
        $query->orderBy($orderBy, $direction);

        $paginatedPatients = $query->paginate($perPage)->withQueryString();

        // Transformação dos dados
        $patients = $paginatedPatients->getCollection()->map(function($user) {
            $engagement = min(100, $user->food_entries_count * 7.5);

            $weights = $user->weightEntries;
            $weightEvo = 0;
            $lastWeight = '--';
            if ($weights->isNotEmpty()) {
                $lastWeight = $weights->first()->weight_kg;
                if ($weights->count() >= 2) {
                    $weightEvo = $weights[0]->weight_kg - $weights[1]->weight_kg;
                }
            }

            $assessments = $user->assessments;
            $fatEvo = 0;
            if ($assessments->count() >= 2) {
                $fatEvo = $assessments[0]->bf_percent - $assessments[1]->bf_percent;
            }

            $nameParts = explode(' ', trim($user->name));
            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? mb_substr(end($nameParts), 0, 1) : ''));

            $roles = $user->roles->pluck('name')->toArray();
            $isStudent = in_array('paciente', $roles);
            $isPatient = in_array('paciente', $roles);
            $profileType = 'Paciente';
            if ($isStudent && $isPatient) $profileType = 'Paciente + Paciente';
            elseif ($isStudent) $profileType = 'Paciente';

            // Requirement: "Mostrar status do paciente/aluno (Pendente, Ativo, Inativo)"
            $currentStatus = 'Inativo';
            if ($user->pivot->status === 'Sim') {
                if ($user->status === 'pending') {
                    $currentStatus = 'Pendente';
                } elseif ($user->status === 'active') {
                    $currentStatus = 'Ativo';
                }
            }

            $isOverLimit = auth()->user()->isResourceOverLimit('patients', $user->id);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'initials' => $initials,
                'status' => $currentStatus,
                'engage_val' => (int)$engagement,
                'last_weight' => $lastWeight,
                'weight_evo' => ($weightEvo > 0 ? '+' : '') . number_format($weightEvo, 1),
                'fat_evo' => ($fatEvo > 0 ? '+' : '') . number_format($fatEvo, 1),
                'goal' => match($user->profile->goal ?? '') {
                    'maintain' => 'Saúde e Bem-Estar',
                    'gain' => 'Hipertrofia',
                    'lose' => 'Emagrecimento',
                    'recomp' => 'Recomposição Corporal',
                    'performance' => 'Performance',
                    default => $user->profile->goal ?? 'Saúde e Bem-Estar'
                },
                'last_activity' => $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Nunca acessou',
                'last_activity_date' => $user->last_activity_at ? $user->last_activity_at->format('d/m/Y H:i') : '--',
                'color' => $isOverLimit ? 'rose' : ($currentStatus === 'Inativo' ? 'zinc' : ($currentStatus === 'Ativo' ? 'emerald' : 'amber')),
                'user_status' => $user->status,
                'profile_type' => $profileType,
                'professional_name' => auth()->user()->name,
                'is_locked' => $isOverLimit,
            ];
        });

        $paginatedPatients->setCollection($patients);

        return view('professional.patients.index', [
            'patients' => $paginatedPatients,
            'total' => $paginatedPatients->total(),
        ]);
    }

    /**
     * Exibe o prontuário eletrônico (EHR) detalhado do paciente.
     */
    public function show(User $patient): View
    {
        $this->authorizePatient($patient);

        if (auth()->user()->isResourceOverLimit('patients', $patient->id)) {
            return back()->with('error', 'Este paciente/aluno está bloqueado por exceder o limite do seu plano atual. Faça upgrade para acessá-lo.');
        }

        // SETA O ALUNO COMO ATIVO NA SESSÃO DO PROFISSIONAL
        session(['active_patient_id' => $patient->id]);

        $user = $patient->load(['profile', 'weightEntries']);
        
        // Registro de Auditoria (Item 13)
        $user->logAccess('view_patient_ehr');

        $profile = $user->profile;
        
        $patient = [
            'id' => $user->id,
            'name' => $user->name,
            'age' => $profile && $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->age : 'N/A',
            'height' => $profile->height_cm ?? 'N/A',
            'weight' => $user->weightEntries()->latest()->first()?->weight_kg ?? 'N/A',
            'bf' => $user->assessments()->orderBy('assessment_date', 'desc')->value('bf_percent') ?? 'N/A',
            'formula' => 'Cunningham',
            'activity_level' => $profile->activity_level ?? 'Não definido',
            'goal' => match($profile->goal ?? '') {
                'maintain' => 'Saúde e Bem-Estar',
                'gain' => 'Hipertrofia',
                'lose' => 'Emagrecimento',
                'recomp' => 'Recomposição Corporal',
                'performance' => 'Performance',
                default => $profile->goal ?? 'Não definido'
            },
            'biotype' => 'Mesomorfo', // Placeholder
        ];

        // Dados de evolução para o gráfico (Reais)
        $assessments = $user->assessments()->orderBy('assessment_date', 'asc')->get();
        
        $chartData = [
            'dates' => [],
            'weight' => [],
            'bf' => [],
        ];

        $latest = null;
        $deltaWeight = 0;
        $deltaBf = 0;

        foreach ($assessments as $index => $assessment) {
            $prev = $assessments->get($index - 1);
            if ($index === $assessments->count() - 1) {
                $latest = $assessment;
                $deltaWeight = $prev ? $assessment->weight_kg - $prev->weight_kg : 0;
                $deltaBf = $prev ? $assessment->bf_percent - $prev->bf_percent : 0;
            }
            
            $chartData['dates'][] = $assessment->assessment_date->format('d/m');
            $chartData['weight'][] = (float) $assessment->weight_kg;
            $chartData['bf'][] = (float) $assessment->bf_percent;
        }

        // Fallback se não houver avaliações, tenta buscar do WeightEntry
        if (empty($chartData['dates'])) {
            $weights = $user->weightEntries()->orderBy('weighed_at', 'asc')->limit(10)->get();
            foreach ($weights as $w) {
                $chartData['dates'][] = $w->weighed_at->format('d/m');
                $chartData['weight'][] = (float) $w->weight_kg;
                $chartData['bf'][] = null;
            }
        }

        $gender = $profile->sex ?? 'M';

        return view('professional.patients.show', compact('patient', 'chartData', 'latest', 'deltaWeight', 'deltaBf', 'gender'));
    }

    public function checkExisting(Request $request)
    {
        $cpf = \App\Support\Cpf::normalize($request->input('cpf'));
        $email = $request->input('email');
        
        $userByCpf = $cpf ? \App\Models\User::withoutGlobalScopes()->where('cpf', $cpf)->first() : null;
        $userByEmail = $email ? \App\Models\User::withoutGlobalScopes()->where('email', $email)->first() : null;
        
        $existingUser = $userByCpf ?: $userByEmail;
        
        if ($existingUser) {
            if ($existingUser->id === auth()->id() || $existingUser->hasRole('professional')) {
                return response()->json(['exists' => true, 'can_reactivate' => false, 'message' => 'Profissional não pode ser cadastrado como paciente.']);
            }
            
            $activeLink = auth()->user()->patients()->wherePivot('user_id', $existingUser->id)->wherePivot('status', 'Sim')->exists();
            
            if ($activeLink) {
                return response()->json(['exists' => true, 'can_reactivate' => false, 'message' => 'Este paciente/aluno já está vinculado ativamente ao seu painel.']);
            }
            
            return response()->json([
                'exists' => true,
                'can_reactivate' => true,
                'name' => $existingUser->name,
                'message' => 'Tivemos um cadastro anterior encontrado para este aluno/paciente. Deseja reativar e vinculá-lo ao seu painel?'
            ]);
        }
        
        return response()->json(['exists' => false]);
    }

    public function create()
    {
        return view('professional.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'cpf' => ['required', 'string', new \App\Rules\CpfValido()],
            'phone' => 'required|string|max:20',
            'goal' => 'required|string',
            'sex' => 'required|in:M,F',
            'birth_date' => 'required|date|before:today',
            'force_reactivate' => 'nullable|boolean',
        ]);

        $cpf = \App\Support\Cpf::normalize($validated['cpf']);

        $professional = auth()->user();
        $maxPatients = $professional->getPlanLimit('max_patients') ?: $professional->getPlanLimit('max_students');
        
        if ($maxPatients > 0) {
            $patientCount = $professional->patients()->count();
            if ($patientCount >= $maxPatients) {
                return back()->withInput()->with('error', "Você atingiu o limite de {$maxPatients} pacientes/pacientes no seu plano. Faça upgrade para continuar cadastrando!");
            }
        }

        $activationLink = null;

        try {
            DB::transaction(function() use ($validated, $cpf, &$activationLink) {
                // Regra: Não permitir profissional como paciente
                $userByCpf = \App\Models\User::withoutGlobalScopes()->where('cpf', $cpf)->first();
                $userByEmail = \App\Models\User::withoutGlobalScopes()->where('email', $validated['email'])->first();

                $existingUser = $userByCpf ?: $userByEmail;

                if ($existingUser) {
                    if ($existingUser->id === auth()->id()) {
                        throw new \Exception('Um profissional não pode ser paciente/aluno dele mesmo.');
                    }
                    if ($existingUser->hasRole('professional')) {
                        throw new \Exception('Um profissional não pode ser cadastrado como paciente/aluno.');
                    }
                    
                    if (empty($validated['force_reactivate'])) {
                        throw new \Exception('Já existe um usuário cadastrado com este e-mail ou CPF. Verifique os dados informados ou utilize a opção de recuperação de acesso.');
                    }
                }

            $user = $existingUser;

            if (!$user) {
                // Tenta gerar um username único baseado no e-mail
                $baseUsername = explode('@', $validated['email'])[0];
                $username = $baseUsername;
                $counter = 1;
                while (\App\Models\User::withoutGlobalScopes()->where('username', $username)->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $user = new \App\Models\User([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'cpf' => $cpf,
                    'phone' => $validated['phone'],
                    'username' => $username,
                    'onboarding_status' => 'pending',
                    'perfil_paciente_completo' => false,
                    'status' => 'pending',
                    'plan_id' => \App\Models\Plan::where('name', 'Free')->value('id'),
                    'academy_company_id' => auth()->user()->academy_company_id,
                ]);
                $user->password_hash = \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16));
                $user->save();
            } else {
                // Se o usuário já existe, atualizamos o nome para o informado pelo profissional nesta ficha,
                // caso o nome atual seja genérico ou incompleto.
                $user->update([
                    'name' => $user->name ?: $validated['name'],
                    'cpf' => $user->cpf ?: $cpf,
                    'academy_company_id' => $user->academy_company_id ?: auth()->user()->academy_company_id
                ]);
            }

            // Garante o perfil de Paciente (Item: "O sistema deve automaticamente definir o perfil como Paciente")
            $user->assignRole('paciente');

            // Vincula ao profissional (Item: "Vincular o paciente ao profissional que realizou o cadastro")
            auth()->user()->patients()->syncWithoutDetaching([$user->id => [
                'data_cadastro' => now(), // Keeps original date if existing, else sets now
                'status' => 'Sim',
                'data_fim' => null,
                'motivo_desvinculacao' => null,
                'empresa_id' => auth()->user()->academy_company_id
            ]]);
            
            // Força a atualização dos campos se for reativação (syncWithoutDetaching não atualiza campos extras se a linha já existir)
            if (!empty($validated['force_reactivate'])) {
                auth()->user()->patients()->updateExistingPivot($user->id, [
                    'status' => 'Sim',
                    'data_fim' => null,
                    'motivo_desvinculacao' => null
                ]);
                $user->status = 'active'; // Se o usuário estava inativo globalmente, reativa
                $user->save();
            }

            // Log de auditoria (Item 9 e 13)
            $user->logAccess(!empty($validated['force_reactivate']) ? 'reactivate_link' : 'create_link');

            // Perfil de saúde (dados antropométricos)
            $user->profile()->updateOrCreate(['user_id' => $user->id], [
                'goal' => $validated['goal'],
                'sex' => $validated['sex'],
                'birth_date' => $validated['birth_date'],
            ]);

            // Se o usuário for NOVO ou PENDENTE, gera o token de ativação
            // (Item: "Gerar um link único de acesso. Enviar esse link ao paciente")
            if ($user->status === 'pending') {
                $activationLink = $this->createActivationToken($user);
            }
        });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->withInput()->with('error', 'Erro de duplicidade: Este CPF ou E-mail já está em uso por outro usuário.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $redirect = redirect()->route('professional.patients.index')
            ->with('success', 'Paciente/Aluno vinculado com sucesso.');

        if ($activationLink) {
            $redirect->with('activation_link', $activationLink);
        }

        return $redirect;
    }

    public function edit(User $patient)
    {
        $this->authorizePatient($patient, 'update');

        if (auth()->user()->isResourceOverLimit('patients', $patient->id)) {
            return back()->with('error', 'Este paciente/aluno está bloqueado por exceder o limite do seu plano atual. Faça upgrade para editá-lo.');
        }

        $patient->load('profile');

        return view('professional.patients.edit', compact('patient'));
    }

    public function update(Request $request, User $patient)
    {
        $this->authorizePatient($patient, 'update');

        if (auth()->user()->isResourceOverLimit('patients', $patient->id)) {
            return back()->with('error', 'Este paciente/aluno está bloqueado por exceder o limite do seu plano atual. Faça upgrade para editá-lo.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->id,
            'goal' => 'required|string',
            'sex' => 'required|in:M,F',
            'birth_date' => 'required|date|before:today',
        ]);

        DB::transaction(function() use ($patient, $validated) {
            $patient->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            $patient->profile()->updateOrCreate(
                ['user_id' => $patient->id],
                [
                    'goal' => $validated['goal'],
                    'sex' => $validated['sex'],
                    'birth_date' => $validated['birth_date'],
                ]
            );
        });

        return redirect()->route('professional.patients.index')
            ->with('success', 'Cadastro do paciente/aluno atualizado com sucesso.');
    }

    public function transfer(Request $request, User $patient)
    {
        $this->authorizePatient($patient, 'update');

        $request->validate([
            'professional_code' => 'required|exists:users,professional_code',
        ]);

        $newProfessional = \App\Models\User::where('professional_code', $request->professional_code)->firstOrFail();
        $currentProfessional = auth()->user();

        DB::transaction(function() use ($patient, $newProfessional) {
            // Cria solicitação de transferência
            \App\Models\ProfessionalPatientRequest::updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'professional_id' => $newProfessional->id,
                    'status' => 'pending'
                ],
                [
                    'request_date' => now(),
                    'message' => 'Transferência'
                ]
            );
            
            // Log de auditoria
            $patient->logAccess('requested_transfer_professional');
        });

        return redirect()->route('professional.patients.index')->with('success', 'Solicitação de transferência enviada ao novo profissional.');
    }

    public function deactivate(Request $request, User $patient)
    {
        $request->validate([
            'motivo_desvinculacao' => 'nullable|string|max:500'
        ]);

        $professional = auth()->user();
        $this->authorizePatient($patient, 'update');

        DB::transaction(function() use ($request, $professional, $patient) {
            // 1. Inativa o vínculo na tabela pivô
            $professional->patients()->updateExistingPivot($patient->id, [
                'status' => 'Não',
                'data_fim' => now(),
                'motivo_desvinculacao' => $request->input('motivo_desvinculacao')
            ]);
            
            // 2. Limpa da sessão se for o paciente ativo no momento
            if (session('active_patient_id') == $patient->id) {
                session()->forget('active_patient_id');
            }

            // 3. Cancela agendamentos futuros
            \App\Models\ProfessionalAppointment::where('professional_id', $professional->id)
                ->where('patient_id', $patient->id)
                ->where('appointment_at', '>', now())
                ->update(['status' => 'cancelled']);

            // 4. Inativa os treinos do paciente prescritos por esse profissional
            \App\Models\TrainingPlan::where('professional_id', $professional->id)
                ->where('user_id', $patient->id)
                ->update(['is_active' => false]);
                
            // 5. Log de auditoria
            $patient->logAccess('deactivate_link');
        });

        return back()->with('success', 'Paciente desvinculado com sucesso. Histórico mantido e pendências canceladas.');
    }

    /**
     * Gera um link de ativação para um paciente pendente.
     */
    public function resendActivationLink(User $patient)
    {
        $this->authorizePatient($patient);

        if ($patient->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Este paciente/aluno já está ativo ou não está pendente.'
            ], 400);
        }

        $link = $this->createActivationToken($patient);

        return response()->json([
            'success' => true,
            'link' => $link,
            'message' => 'Link de ativação gerado com sucesso.'
        ]);
    }

    /**
     * Helper para criar token de ativação e retornar o link.
     */
    private function createActivationToken(\App\Models\User $user): string
    {
        // Inativa tokens anteriores de qualquer tipo para este paciente (Item 7)
        $user->accessTokens()->where('status', 'active')->update(['status' => 'revoked']);

        $token = \Illuminate\Support\Str::random(64);
        
        $user->accessTokens()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addHours(24),
            'status' => 'active',
            'type' => 'activation'
        ]);

        $url = route('patient.activate.show', ['token' => $token]);

        // Envia notificação por e-mail
        try {
            $user->notify(new \App\Notifications\PatientActivationLink($url, $user->name));
        } catch (\Exception $e) {
            // Log do erro silencioso para não interromper o fluxo se o e-mail falhar
            \Illuminate\Support\Facades\Log::error('Falha ao enviar e-mail de ativação: ' . $e->getMessage());
        }

        return $url;
    }

    /**
     * Gera um link de acesso seguro para o portal do paciente.
     */
    public function generateAccessLink(User $patient)
    {
        $this->authorizePatient($patient);

        // Gera um token aleatório e seguro
        $token = \Illuminate\Support\Str::random(32);
        
        // Salva o hash no banco (Regra 25)
        $patient->accessTokens()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(7), // Token válido por 7 dias
            'status' => 'active',
            'type' => 'access'
        ]);

        $link = route('access', ['token' => $token]);

        return response()->json([
            'success' => true,
            'link' => $link
        ]);
    }

    /**
     * Define o paciente ativo na sessão do profissional.
     */
    public function setActivePatient(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id'
        ]);

        $professional = auth()->user();
        $patientId = $request->patient_id;

        // Verifica se o profissional tem acesso a este paciente
        $hasAccess = \App\Models\ProfessionalPatient::where('profissional_id', $professional->id)
            ->where('user_id', $patientId)
            ->whereIn('status', ['Sim', 'PENDENTE'])
            ->exists();

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para acessar este paciente/aluno.'
            ], 403);
        }

        session(['active_patient_id' => $patientId]);

        // Atualizar o timestamp de último acesso no pivot
        \App\Models\ProfessionalPatient::where('profissional_id', $professional->id)
            ->where('user_id', $patientId)
            ->update(['last_accessed_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Paciente ativo atualizado com sucesso.'
        ]);
    }

    /**
     * Limpa o paciente ativo da sessão.
     */
    public function clearActivePatient()
    {
        session()->forget('active_patient_id');
        session()->save();

        return response()->json([
            'success' => true,
            'message' => 'Paciente ativo removido.'
        ]);
    }

    /**
     * Busca pacientes via JSON para seleção.
     */
    public function search(Request $request)
    {
        $professional = auth()->user();
        $query = $request->get('q', '');

        $patientsQuery = $professional->patients()
            ->wherePivotIn('status', ['active', 'pending']);

        if (!empty($query)) {
            $patientsQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('cpf', 'like', "%{$query}%");
            });
        }

        $patients = $patientsQuery->limit(10)->get(['users.id', 'users.name', 'users.email', 'users.photo_url']);

        return response()->json($patients);
    }

    public function approve(\Illuminate\Http\Request $request, User $patient)
    {
        $professional = auth()->user();

        $pivot = $professional->patients()->where('users.id', $patient->id)->first()?->pivot;

        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Paciente não encontrado.'], 403);
        }

        if ($patient->status !== 'pending' && $pivot->status !== 'PENDENTE') {
            return response()->json(['success' => false, 'message' => 'O paciente já não está pendente.']);
        }

        $generatedPassword = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($patient, $professional, $pivot, &$generatedPassword) {
            
            // Se o paciente foi cadastrado pelo profissional (status = pending, pivot = Sim)
            // Ele precisa de uma senha temporária
            if ($patient->status === 'pending' && $pivot->status === 'Sim') {
                $generatedPassword = \Illuminate\Support\Str::password(16, true, true, true, false);
                $patient->password_hash = \Illuminate\Support\Facades\Hash::make($generatedPassword);
                $patient->force_password_change = true;
                $patient->temp_password_expires_at = now()->addHours(24);
            }

            // Ativa o usuário globalmente
            $patient->status = 'active';
            if (!$patient->activated_at) {
                $patient->activated_at = now();
            }
            $patient->save();

            // Atualiza o status na tabela pivô se for PENDENTE (paciente que se cadastrou sozinho)
            if ($pivot->status === 'PENDENTE') {
                $professional->patients()->updateExistingPivot($patient->id, ['status' => 'Sim']);
            }
        });

        // Enviar e-mail de boas-vindas com a senha temporária se foi gerada
        if ($generatedPassword) {
            try {
                app(\App\Services\TransactionalMailService::class)->sendToUser(
                    new \App\Mail\ForcedPasswordResetUserMail($patient, $generatedPassword),
                    $patient,
                    $patient->academy_company_id,
                    \App\Enums\MailSendType::PASSWORD_RESET,
                    'Seu acesso foi liberado',
                    'Liberação de acesso por profissional'
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erro ao enviar e-mail de aprovação de paciente: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'type' => 'generated',
                'message' => 'Acesso liberado com sucesso. Senha provisória enviada por e-mail.',
            ]);
        }

        // Se não gerou senha, apenas liberou o vínculo
        return response()->json([
            'success' => true,
            'type' => 'approved',
            'message' => 'Vínculo com o paciente aprovado e liberado com sucesso!',
        ]);
    }

    private function authorizePatient(User $patient, string $ability = 'view'): void
    {
        $this->authorize("professionalPatient.{$ability}", $patient);
    }
}





