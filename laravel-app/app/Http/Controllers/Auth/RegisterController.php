<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Rules\CpfValido;
use App\Support\Cpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use App\Models\UserConsent;
use App\Models\UserProfile;
use App\Notifications\NewStudentRegistrationPending;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isAdministrator()) {
                return redirect()->route('dashboard');
            }

            if ($user->isRegistrationPending()) {
                return redirect()->route('registration.pending');
            }

            if ($user->isRegistrationRejected()) {
                return redirect()->route('registration.rejected');
            }

            if ($user->email_verified_at) {
                return redirect()->route('dashboard');
            }

            return redirect()->route($user->hasRole('professional') ? 'professional.dashboard' : 'dashboard');
        }

        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->merge([
            'cpf' => Cpf::normalize($request->input('cpf')),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'tipo_acesso' => ['required', 'string', 'in:aluno,professional,manager,representative'],
            'cpf' => ['required_unless:tipo_acesso,manager', 'nullable', 'string', 'size:11', new CpfValido()],
            'cnpj' => ['required_if:tipo_acesso,manager', 'nullable', 'string', 'max:18'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['required', 'accepted'],
            // Campos adicionais Profissional
            'profession_id' => ['required_if:tipo_acesso,professional', 'nullable', 'exists:professions,id'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:120'],
            // Campos opcionais
            'phone' => ['nullable', 'string', 'max:25'],
            'birth_date' => ['required_unless:tipo_acesso,manager', 'nullable', 'date', 'before:today'],
            'sex' => ['required_unless:tipo_acesso,manager', 'nullable', 'string', 'in:M,F'],
        ], [
            'email.unique' => 'Este e-mail já possui cadastro no sistema.',
            'cpf.unique' => 'Este CPF já possui cadastro no sistema.',
            'tipo_acesso.required' => 'Selecione o tipo de acesso para continuar.',
            'profession_id.required_if' => 'Selecione o tipo de profissional.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Não foi possível concluir o cadastro.',
                    'errors' => $validator->errors(),
                    'duplicate_registration' => $this->duplicateRegistrationHint($validator),
                ], 422);
            }

            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        $user = DB::transaction(function () use ($validated, $request) {
            $profileName = $validated['tipo_acesso'];
            $role = \App\Models\Role::where('name', $profileName)->first();
            $freePlan = \App\Models\Plan::where('name', 'Free')->first();

            // Regra 1 e 2: O sistema deve utilizar CPF como identificador único da pessoa.
            $user = User::where('cpf', $validated['cpf'])->first();

            // Fallback: se não achar por CPF, tenta por e-mail 
            if (!$user) {
                $user = User::where('email', $validated['email'])->first();
            }

            if ($user) {
                // Vincular papel na tabela pivot (Many-to-Many)
                if ($role) {
                    $user->assignRole($role->name);
                }

                if ($profileName === 'aluno') {
                    app(\App\Services\StudentRoleBridgeService::class)->ensurePortalAccess($user);
                }
                
                // Atualizar CPF caso o usuário encontrado pelo e-mail não tivesse CPF
                if (empty($user->cpf)) {
                    $user->update(['cpf' => $validated['cpf']]);
                }

                return $user;
            }

            $verificacaoAtiva = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);

            $representativeId = $request->get('representative_id') ?: (session('representative_id') ?: request()->cookie('representative_id'));
            
            if ($request->filled('referral_code')) {
                $profile = \App\Models\RepresentativeProfile::where('code', strtoupper($request->get('referral_code')))->first();
                if ($profile) {
                    $representativeId = $profile->user_id;
                }
            }

            $user = new User();
            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'cpf' => $validated['cpf'] ?? null,
                'cnpj' => $validated['cnpj'] ?? null,
                'profile_id' => $role?->id, // Compatibilidade
                'plan_id' => $freePlan?->id,
                'status' => $profileName === 'representative' ? 'PENDENTE_APROVACAO' : ($verificacaoAtiva ? 'pending_email_verification' : 'active'),
                'onboarding_status' => 'pending',
                'profile_completion_percentage' => 0,
                'registration_approval_status' => $profileName === 'representative' ? 'pending' : 'approved',
                'email_verified' => !$verificacaoAtiva,
                'email_verified_at' => $verificacaoAtiva ? null : now(),
                'email_verification_expires_at' => $verificacaoAtiva ? now()->addHours(24) : null,
                'representative_id' => $representativeId,
                'is_representative' => $profileName === 'representative',
            ]);

            // Vincular à clínica ou empresa se houver slug na request
            if ($request->filled('clinic')) {
                $clinic = \App\Models\Clinic::where('slug', $request->get('clinic'))->first();
                if ($clinic) {
                    $user->clinic_id = $clinic->id;
                    $user->academy_company_id = $clinic->academy_company_id;
                }
            } elseif ($request->filled('company_slug')) {
                $company = \App\Models\AcademyCompany::where('slug', $request->get('company_slug'))->first();
                if ($company) {
                    $user->academy_company_id = $company->id;
                }
            }

            $user->password_hash = Hash::make($validated['password']);
            $user->save();

            // Vincular papel na tabela pivot (Many-to-Many)
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            if ($profileName === 'aluno') {
                app(\App\Services\StudentRoleBridgeService::class)->ensurePortalAccess($user);
            }
            
            // Perfil básico
            UserProfile::create([
                'user_id' => $user->id,
                'birth_date' => $validated['birth_date'] ?? null,
                'sex' => $validated['sex'] ?? '',
            ]);

            // Perfil Profissional se selecionado (campos NOT NULL na BD: preencher com vazio/placeholder até completar no painel)
            if ($profileName === 'professional') {
                \App\Models\ProfessionalProfile::create([
                    'user_id' => $user->id,
                    'profession_id' => $validated['profession_id'],
                    'specialty' => $validated['specialty'] ?? null,
                    'registration_number' => $validated['registration_number'] ?? '',
                    'council' => $validated['council'] ?? '',
                    'registration_uf' => strtoupper(substr((string) ($validated['registration_uf'] ?? 'NA'), 0, 2)) ?: 'NA',
                    'registration_expiry_date' => ! empty($validated['registration_expiry_date'])
                        ? $validated['registration_expiry_date']
                        : now()->addYears(10)->toDateString(),
                    'company_name' => $validated['company_name'] ?? null,
                ]);
            }

            // Registrar Consentimento Inicial (LGPD)
            UserConsent::create([
                'user_id' => $user->id,
                'consent_type' => 'privacy_policy_and_terms',
                'version' => '1.0',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            // Lógica Comercial: Converter Proposta e Gerar Comissão Prevista
            if ($representativeId) {
                // Procurar uma proposta ativa deste representante para este CNPJ ou Email
                $proposal = \App\Models\CommercialProposal::where('representative_id', $representativeId)
                    ->where('status', 'Ativa')
                    ->where(function ($q) use ($validated) {
                        if (!empty($validated['cnpj'])) {
                            $q->where('clinic_cnpj', preg_replace('/[^0-9]/', '', $validated['cnpj']))
                              ->orWhere('clinic_cnpj', $validated['cnpj']);
                        }
                    })
                    ->latest()
                    ->first();

                if ($proposal) {
                    $proposal->update(['status' => 'Convertida em Cliente']);
                    
                    // Se for clinic_id que vai ser criado depois, podemos associar aqui se já existisse.
                    // Como a clínica pode ser criada no onboarding, associamos ao usuário por enquanto.
                }

                // Criar Comissão Prevista baseada no perfil do representante e no plano (se houver plano na proposta)
                if (isset($profile) && $profile) {
                    $baseAmount = $proposal ? (float) $proposal->valor_final : 0.0;

                    app(\App\Services\CommissionService::class)->recordProspectiveOnRegistration(
                        $representativeId,
                        $user->id,
                        $baseAmount,
                        (float) $profile->commission_rate,
                        'Cadastro realizado via código de indicação: '.$profile->code
                    );

                    $profile->incrementUsage();
                }
            }

            return $user;
        });

        // Limpar dados de onboarding após o uso
        session()->forget('onboarding_data');

        // Se a verificação de e-mail estiver ativa, não logamos o usuário ainda se quisermos bloquear o acesso total.
        // Mas o requisito 7 diz "Bloquear acesso se: status != ATIVO".
        // Se quisermos que ele possa ver a página de "verifique seu e-mail", ele precisa estar logado ou identificável.
        // Laravel costuma logar e depois o middleware redireciona.
        
        $verificacaoAtiva = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);

        if (!$verificacaoAtiva) {
            $request->session()->regenerate();
            Auth::login($user);
        }

        $adminRecipients = User::query()
            ->where(function ($q) {
                $q->where('is_admin', true)
                    ->orWhereHas('userProfile.permissions', function ($p) {
                        $p->where('name', 'admin.access');
                    });
            })
            ->get()
            ->unique('id');

        foreach ($adminRecipients as $admin) {
            try {
                $admin->notify(new NewStudentRegistrationPending($user));
            } catch (\Throwable $e) {
                Log::warning('Falha ao notificar admin sobre novo cadastro', [
                    'admin_id' => $admin->id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        // Notificar o representante sobre o recebimento do cadastro
        if ($user->hasRole('representative')) {
            try {
                $user->notify(new \App\Notifications\RepresentativeRegistered());
            } catch (\Throwable $e) {
                Log::warning('Falha ao notificar representante sobre cadastro recebido', [
                    'user_id' => $user->id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        $emailSent = false;
        if ($verificacaoAtiva) {
            try {
                $emailSent = app(\App\Services\EmailVerificationService::class)->sendVerificationEmail($user);
            } catch (\Throwable $e) {
                Log::error('Cadastro: falha ao enviar e-mail de verificação', [
                    'user_id' => $user->id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        if ($emailSent) {
            session()->flash(
                'success',
                'Enviamos um link de confirmação para o seu e-mail. Verifique a caixa de entrada e o spam.'
            );
        }

        if ($verificacaoAtiva) {
            $postRegisterRedirect = route('verification.notice', ['email' => $user->email]);
        } else {
            // Requisito: Geração Automática de Link de Acesso Direto
            $accessLink = null;
            if (config('system.onboarding.enabled', true)) {
                try {
                    $service = app(\App\Services\SystemAccessService::class);
                    $accessLink = $service->generateForUser($user);
                    
                    if (config('system.onboarding.send_email', true)) {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeSystemAccessMail($user, $accessLink));
                    }
                } catch (\Throwable $e) {
                    Log::error('Falha ao gerar link de acesso ou enviar e-mail de boas-vindas', [
                        'user_id' => $user->id,
                        'exception' => $e->getMessage()
                    ]);
                }
            }

            if ($accessLink && app()->environment('production')) {
                $postRegisterRedirect = route('onboarding.welcome-access');
            } elseif ($user->registration_approval_status === 'approved') {
                $postRegisterRedirect = route($user->hasRole('professional') ? 'professional.dashboard' : 'dashboard');
            } else {
                $postRegisterRedirect = $user->isRepresentativePending() 
                    ? route('representative.pending') 
                    : route('registration.pending');
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'email' => $user->email,
                'email_sent' => $emailSent,
                'message' => $emailSent
                    ? 'Enviamos um link de confirmação para o seu e-mail.'
                    : 'Não foi possível enviar o e-mail agora. Use Reenviar na página seguinte.',
                // Sessão regenerada após o cadastro: o token dos formulários da página deve ser atualizado.
                'csrf_token' => csrf_token(),
                'redirect' => $postRegisterRedirect,
            ]);
        }

        return redirect()->to($postRegisterRedirect);
    }

    /**
     * Indica se o 422 foi por e-mail ou CPF já cadastrados (para popup no frontend).
     */
    private function duplicateRegistrationHint(\Illuminate\Validation\Validator $validator): ?string
    {
        $errors = $validator->errors();
        $emailDup = $errors->has('email')
            && str_contains((string) $errors->first('email'), 'já possui cadastro');
        $cpfMsgs = $errors->get('cpf', []);
        $cpfDup = false;
        foreach ($cpfMsgs as $msg) {
            if (str_contains((string) $msg, 'já possui cadastro')) {
                $cpfDup = true;
                break;
            }
        }

        if ($emailDup && $cpfDup) {
            return 'both';
        }
        if ($emailDup) {
            return 'email';
        }
        if ($cpfDup) {
            return 'cpf';
        }

        return null;
    }
}
