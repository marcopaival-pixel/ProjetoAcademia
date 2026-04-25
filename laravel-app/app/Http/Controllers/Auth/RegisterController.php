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
            'cpf' => ['required', 'string', 'size:11', new CpfValido()],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['required', 'accepted'],
            'tipo_acesso' => ['required', 'string', 'in:aluno,professional'],
            // Campos adicionais Profissional
            'profession_id' => ['required_if:tipo_acesso,professional', 'nullable', 'exists:professions,id'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:120'],
            // Campos opcionais
            'phone' => ['nullable', 'string', 'max:25'],
            'birth_date' => ['required', 'date', 'before:today'],
            'sex' => ['required', 'string', 'in:M,F'],
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

        $user = DB::transaction(function () use ($validated) {
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
                
                // Atualizar CPF caso o usuário encontrado pelo e-mail não tivesse CPF
                if (empty($user->cpf)) {
                    $user->update(['cpf' => $validated['cpf']]);
                }

                return $user;
            }

            $user = new User();
            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'cpf' => $validated['cpf'],
                'profile_id' => $role?->id, // Compatibilidade
                'plan_id' => $freePlan?->id,
                'status' => 'active',
                'onboarding_status' => 'pending',
                'profile_completion_percentage' => 0,
                'registration_approval_status' => $profileName === 'professional' ? 'approved' : 'pending',
                'email_verified_at' => now(), // Verificação suspensa temporariamente
            ]);

            // Vincular à empresa se houver slug na request
            if ($request->filled('company_slug')) {
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
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);

            return $user;
        });

        // Limpar dados de onboarding após o uso
        session()->forget('onboarding_data');

        $request->session()->regenerate();
        Auth::login($user);

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

        $emailSent = false;
        /* Verificação suspensa
        try {
            $emailSent = app(\App\Services\EmailVerificationService::class)->sendVerificationEmail($user);
        } catch (\Throwable $e) {
            Log::error('Cadastro: falha ao enviar e-mail de verificação', [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);
        }
        */

        if ($emailSent) {
            session()->flash(
                'success',
                'Enviamos um link de confirmação para o seu e-mail. Verifique a caixa de entrada e o spam.'
            );
        }

        $postRegisterRedirect = $user->registration_approval_status === 'approved'
            ? route($user->hasRole('professional') ? 'professional.dashboard' : 'dashboard')
            : route('registration.pending');

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
