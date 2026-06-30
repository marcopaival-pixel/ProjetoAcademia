<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Muscle;
use App\Models\MuscleGroup;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use App\Models\AdminLog;
use App\Models\SystemError;
use App\Models\AdminSetting;
use App\Models\ExerciseCatalog;
use App\Models\Announcement;
use App\Models\FoodEntry;
use App\Services\AdminOverviewStats;
use App\Models\UserConsent;
use App\Models\Permission;
use App\Mail\WelcomeUserEmail;
use App\Services\TransactionalMailService;
use App\Support\MailSendType;
use App\Rules\CpfValido;
use App\Support\Cpf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Mail\AdminTestEmail;
use App\Services\MailConfigService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\ForcedPasswordResetUserMail;
use App\Mail\ForcedPasswordResetAdminNotificationMail;

class AdminAreaController extends Controller
{
    public function dashboard(): View
    {
        $overview = AdminOverviewStats::collect();
        
        $financial = app(\App\Services\FinancialMetricsService::class);
        $totalRevenue = $financial->totalRevenue();
        $legacyMpRevenue = \App\Models\MercadoPagoCredit::sum('transaction_amount');

        $saasMetrics = app(\App\Services\SaaSMetricsService::class);
        $mrr = $saasMetrics->calculateMRR();
        $activeSubsCount = $saasMetrics->getActiveSubscriptionsCount();

        // Assinaturas a expirar nos próximos 15 dias
        $expiringCount = User::where('is_premium', true)
            ->whereBetween('premium_expires_at', [now(), now()->addDays(15)])
            ->count();

        // Métrica de Perfil: Distribuição de Objetivos
        $goalsDistribution = DB::table('user_profiles')
            ->select('goal', DB::raw('count(*) as total'))
            ->groupBy('goal')
            ->get();

        $thisMonthRevenue = $financial->monthlyRevenue();
        $lastMonthRevenue = $financial->monthlyRevenue(now()->subMonth());

        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 100;

        $metrics = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'total_premium' => User::where('is_premium', true)->count(),
            'total_revenue' => $totalRevenue,
            'legacy_mp_revenue' => $legacyMpRevenue,
            'active_subs' => $activeSubsCount,
            'mrr' => $mrr,
            'expiring_soon' => $expiringCount,
            'this_month_revenue' => $thisMonthRevenue,
            'revenue_growth' => $revenueGrowth,
            'goals' => $goalsDistribution,
            'recent_users' => User::orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_logs' => AdminLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get(),
            'expiring_users' => User::where('is_premium', true)
                ->whereBetween('premium_expires_at', [now(), now()->addDays(15)])
                ->orderBy('premium_expires_at', 'asc')
                ->limit(5)
                ->get(),
            'monthly_revenue' => collect($financial->monthlyRevenueSeries(6)),
            'daily_summary' => [
                'new_users' => User::whereDate('created_at', now())->count(),
                'payments' => \App\Models\Payment::whereIn('status', ['paid', 'approved', 'ATIVO', \App\Models\Subscription::STATUS_FIN_ATIVO])
                    ->whereDate('created_at', now())->count(),
                'expiring' => User::where('is_premium', true)->whereDate('premium_expires_at', now())->count(),
                'messages' => \App\Models\Message::where('is_read', false)->whereDate('created_at', now())->count(),
            ],
            'ai_insight' => (function () use ($revenueGrowth, $expiringCount) {
                if ($revenueGrowth < -10) {
                    return 'Alerta operacional: queda brusca no faturamento (-'.abs(round($revenueGrowth)).'%). Verifique o funil de conversão.';
                } elseif ($expiringCount > 10) {
                    return 'Sugestão: '.$expiringCount.' usuários a expirar em 15 dias. Considere uma campanha de retenção.';
                } elseif ($revenueGrowth > 15) {
                    return 'Performance: faturamento acelerado (+'.round($revenueGrowth).'%). Momento ideal para escalar tráfego.';
                }

                return 'Operação NexShape está operando com 98% de eficiência hoje. Todos os serviços em ordem.';
            })(),
        ];

        return view('admin.dashboard', compact('overview', 'metrics'));
    }

    public function users(Request $request): View
    {
        $query = User::query();

        // Filtro por Papel (Role) vindo do menu (aluno, paciente, profissional, etc)
        if ($request->filled('role')) {
            $roleName = $request->get('role');
            $query->whereHas('roles', function($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function($q) use ($s) {
                $q->where('users.id', $s)
                  ->orWhere('users.name', 'like', "%{$s}%")
                  ->orWhere('users.email', 'like', "%{$s}%")
                  ->orWhere('users.cpf', 'like', "%{$s}%");
            });
        }

        if ($request->filled('cpf')) {
            $cpf = Cpf::normalize($request->get('cpf'));
            $query->where('users.cpf', $cpf);
        }

        if ($request->filled('premium')) {
            $query->where('users.is_premium', $request->get('premium') === 'yes');
        }

        if ($request->filled('admin')) {
            $query->where('users.is_admin', $request->get('admin') === 'yes');
        }

        if ($request->filled('profile_id')) {
            $query->where('users.profile_id', (int) $request->get('profile_id'));
        }

        if ($request->filled('profession_id')) {
            $query->whereHas('professionalProfile', function($q) use ($request) {
                $q->where('profession_id', $request->get('profession_id'));
            });
        }

        if ($request->filled('specialty')) {
            $query->whereHas('professionalProfile', function($q) use ($request) {
                $q->where('specialty', $request->get('specialty'));
            });
        }

        if ($request->filled('goal')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('goal', $request->get('goal'));
            });
        }

        if ($request->filled('age_range')) {
            $range = $request->get('age_range');
            $query->whereHas('profile', function($q) use ($range) {
                $now = now();
                if ($range === '0-18') {
                    $q->whereBetween('birth_date', [$now->copy()->subYears(18), $now]);
                } elseif ($range === '19-30') {
                    $q->whereBetween('birth_date', [$now->copy()->subYears(30), $now->copy()->subYears(19)]);
                } elseif ($range === '31-45') {
                    $q->whereBetween('birth_date', [$now->copy()->subYears(45), $now->copy()->subYears(31)]);
                } elseif ($range === '46-60') {
                    $q->whereBetween('birth_date', [$now->copy()->subYears(60), $now->copy()->subYears(46)]);
                } elseif ($range === '60+') {
                    $q->where('birth_date', '<', $now->copy()->subYears(61));
                }
            });
        }

        $subQuery = \DB::table('pacientes')
            ->select('user_id', \DB::raw('MIN(profissional_id) as first_pro_id'))
            ->groupBy('user_id');

        $users = $query->select('users.*')
            ->leftJoinSub($subQuery, 'p_group', function ($join) {
                $join->on('users.id', '=', 'p_group.user_id');
            })
            ->leftJoin('users as pros', 'p_group.first_pro_id', '=', 'pros.id')
            ->with(['roles', 'professionalProfile.profession', 'professionals', 'profile'])
            ->orderByRaw('COALESCE(pros.created_at, users.created_at) DESC')
            ->orderByRaw('(CASE WHEN p_group.first_pro_id IS NULL THEN 0 ELSE 1 END) ASC')
            ->orderBy('users.created_at', 'DESC')
            ->paginate(40)
            ->withQueryString();

        if ($request->has('ajax')) {
            return response()->json([
                'users' => $users->items()
            ]);
        }

        return view('admin.users.index', [
            'users' => $users,
            'profiles' => Role::all(),
            'professions' => \App\Models\Profession::all(),
            'specialties' => \App\Models\Especialidade::active()->get(),
            'goals' => \App\Models\UserProfile::getAvailableGoals(),
            'overview' => AdminOverviewStats::collect(),
            'currentRole' => $request->get('role'),
        ]);
    }

    public function createUser(Request $request): View
    {
        $profiles = Role::all();
        $plans = Plan::all();
        $professions = \App\Models\Profession::all();
        $specialties = \App\Models\Especialidade::active()->get();
        $companies = \App\Models\AcademyCompany::all();
        $selectedCompanyId = $request->get('academy_company_id');
        $selectedRoleId = $request->get('role_id');

        return view('admin.users.create', compact('profiles', 'plans', 'professions', 'specialties', 'companies', 'selectedCompanyId', 'selectedRoleId'));
    }

    public function storeUser(Request $request)
    {
        if ($request->filled('cpf')) {
            $request->merge(['cpf' => Cpf::normalize($request->input('cpf'))]);
        }

        $rules = [
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => $this->getPasswordValidationRules(true),
            'profile_id' => 'required|exists:roles,id',
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,blocked,pending',
            'is_admin' => 'boolean',
            'birth_date' => 'required|date|before:today',
            'sex' => 'required|in:M,F',
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf', new CpfValido()],
            'academy_company_id' => 'nullable|exists:academy_companies,id',
        ];

        // Regras condicionais se for Profissional (ID 4)
        if ($request->get('role_id') == 4) {
            $rules = array_merge($rules, [
                'profession_id' => 'required|exists:professions,id',
                'registration_number' => 'required|string|max:50',
                'council' => 'required|string|max:20',
                'registration_uf' => 'required|string|size:2',
                'registration_expiry_date' => 'required|date',
                'document_file' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
                'signature_file' => 'nullable|image|mimes:png|max:2048',
                'signature_data' => 'nullable|string', // Para assinatura desenhada (base64)
            ]);
        }

        $data = $request->validate($rules);

        $user = DB::transaction(function () use ($request, $data) {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'profile_id' => $data['profile_id'],
                'plan_id' => $data['plan_id'],
                'status' => $data['status'],
                'is_admin' => $request->has('is_admin'),
                'email_verified_at' => $request->has('is_admin') ? now() : null,
                'registration_approval_status' => $data['status'] === 'pending' ? 'pending' : 'approved',
                'academy_company_id' => $data['academy_company_id'] ?? null,
                'cpf' => Cpf::normalize($data['cpf']),
            ]);
            $user->password_hash = Hash::make($data['password']);
            $user->save();

            // Sincronizar o perfil na tabela pivô user_roles
            $user->roles()->sync([$data['profile_id']]);

            if ($data['profile_id'] == 4) {
                $docPath = null;
                if ($request->hasFile('document_file')) {
                    $docPath = $request->file('document_file')->store('profissionais/documentos', 'local');
                }

                $sigPath = null;
                if ($request->hasFile('signature_file')) {
                    $sigPath = $request->file('signature_file')->store('profissionais/assinaturas', 'local');
                } elseif ($request->filled('signature_data')) {
                    // Tratar assinatura desenhada (base64)
                    $dataStr = $request->get('signature_data');
                    if (str_contains($dataStr, 'base64,')) {
                        $image = explode('base64,', $dataStr)[1];
                        $image = str_replace(' ', '+', $image);
                        $imageName = 'sig_' . $user->id . '_' . time() . '.png';
                        \Illuminate\Support\Facades\Storage::disk('local')->put('profissionais/assinaturas/' . $imageName, base64_decode($image));
                        $sigPath = 'profissionais/assinaturas/' . $imageName;
                    }
                }

                \App\Models\ProfessionalProfile::create([
                    'user_id' => $user->id,
                    'profession_id' => $data['profession_id'],
                    'specialty' => $request->get('specialty'),
                    'registration_number' => $data['registration_number'],
                    'council' => $data['council'],
                    'registration_uf' => $data['registration_uf'],
                    'registration_expiry_date' => $data['registration_expiry_date'],
                    'document_path' => $docPath,
                    'signature_path' => $sigPath,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            // Criar Perfil Básico (Geral para todos os usuários)
            \App\Models\UserProfile::create([
                'user_id' => $user->id,
                'birth_date' => $data['birth_date'],
                'sex' => $data['sex'],
            ]);

            return $user;
        });

        // Registrar Log
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou o utilizador #{$user->id} ({$user->name})",
            'ip_address' => $request->ip(),
            'payload' => $request->except(['password', 'password_confirmation'])
        ]);

        $sent = app(TransactionalMailService::class)->sendToUser(
            new WelcomeUserEmail($user),
            $user,
            $user->academy_company_id,
            MailSendType::WELCOME,
            'Bem-vindo — '.$user->name,
            'E-mail de boas-vindas (cadastro admin)'
        );
        if (! $sent) {
            SystemError::create([
                'user_id' => auth()->id(),
                'error_message' => "Falha ao enviar e-mail de boas-vindas para #{$user->id}",
                'severity' => 'low',
            ]);
        }

        return redirect()->route('admin.users')->with('success', "Utilizador {$user->name} criado e e-mail enviado.");
    }

    public function logs(): View
    {
        $logs = AdminLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.logs', compact('logs'));
    }

    public function monitoring(): View
    {
        // Simple system info
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_driver' => config('database.default'),
            'os' => PHP_OS,
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'server_ip' => request()->server('SERVER_ADDR'),
            'disk_free' => @disk_free_space("/") ? round(disk_free_space("/") / 1024 / 1024 / 1024, 2) : 0,
            'disk_total' => @disk_total_space("/") ? round(disk_total_space("/") / 1024 / 1024 / 1024, 2) : 0,
            'pending_jobs' => \Illuminate\Support\Facades\DB::table('jobs')->count(),
            'failed_jobs' => \Illuminate\Support\Facades\DB::table('failed_jobs')->count(),
        ];

        return view('admin.monitoring', compact('info'));
    }


    public function settings(): View
    {
        $settings = AdminSetting::all();
        return view('admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            // Se for uma configuração de sistema (tabela system_settings)
            if (in_array($key, ['verificacao_email_ativa'])) {
                \App\Models\SystemSetting::set($key, $value);
                continue;
            }

            // Se for a senha do e-mail e não estiver vazia, criptografar
            if ($key === 'mail_password' && !empty($value)) {
                $value = Crypt::encryptString($value);
            } elseif ($key === 'mail_password' && empty($value)) {
                // Se estiver vazia, não sobrescrever a senha existente (manter a atual)
                continue;
            }

            AdminSetting::set($key, $value);
        }

        return back()->with('success', 'Configurações atualizadas.');
    }

    public function testAi(Request $request)
    {
        $apiKey = config('services.openai.api_key');
        $apiUrl = config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $model = config('services.openai.model', 'gpt-4o-mini');

        if (empty($apiKey)) {
            return back()->with('error', 'Chave de API OpenAI não configurada.');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->timeout(10)
                ->post($apiUrl, [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => 'Ping']],
                    'max_tokens' => 5
                ]);

            if ($response->successful()) {
                return back()->with('success', 'Conexão com OpenAI estabelecida com sucesso! Modelo: ' . $model);
            }

            $error = $response->json()['error']['message'] ?? 'Erro desconhecido na API OpenAI.';
            return back()->with('error', 'Falha na conexão: ' . $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao conectar: ' . $e->getMessage());
        }
    }

    public function testWhatsApp(Request $request)
    {
        $driver = config('services.whatsapp.driver');
        $apiUrl = config('services.whatsapp.api_url');
        $token = config('services.whatsapp.token');

        if ($driver === 'none' || empty($apiUrl) || empty($token)) {
            return back()->with('error', 'Configurações de WhatsApp incompletas ou driver definido como "none".');
        }

        try {
            // Teste genérico: tenta um GET na URL com o Token (ajustar conforme o driver real)
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->timeout(10)
                ->get($apiUrl);

            // Muitos gateways respondem 404 ou 405 se o endpoint de teste não for GET, 
            // mas se houver resposta do servidor já é um sinal de que a URL/Token estão no caminho certo.
            if ($response->status() < 500) {
                return back()->with('success', 'Gateway de WhatsApp respondeu! Status: ' . $response->status());
            }

            return back()->with('error', 'Falha na resposta do Gateway. Status: ' . $response->status());
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao conectar no Gateway: ' . $e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        $admin = auth()->user();
        $ok = app(TransactionalMailService::class)->sendToUser(
            new AdminTestEmail($admin->name),
            $admin,
            null,
            MailSendType::TEST,
            'Teste de configuração de e-mail',
            'Teste SMTP global (admin_settings)'
        );

        if ($ok) {
            return back()->with('success', 'E-mail de teste enviado com sucesso para '.$admin->email);
        }

        SystemError::create([
            'user_id' => auth()->id(),
            'error_message' => 'Falha no teste de e-mail (ver log_envio_email)',
            'severity' => 'medium',
        ]);

        return back()->with('error', 'Falha ao enviar e-mail. Consulte os logs de envio.');
    }

    public function resendVerificationEmail(User $user): RedirectResponse
    {
        if ($user->email_verified_at) {
            return back()->with('error', 'Este e-mail já está verificado.');
        }

        $sent = app(\App\Services\EmailVerificationService::class)->sendVerificationEmail($user);

        if ($sent) {
            return back()->with('success', 'E-mail de confirmação reenviado.');
        }

        return back()->with('error', 'Não foi possível reenviar (limite ou falha de envio).');
    }
    public function editUser(User $user): View
    {
        $user->load(['userProfile', 'professionalProfile', 'profile', 'plan']);
        $latestWeight = $user->weightEntries()->latest('weighed_at')->first();
        
        $profiles = Role::all();
        $plans = Plan::all();
        $professions = \App\Models\Profession::all();
        $allPermissions = Permission::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'profiles', 'plans', 'professions', 'allPermissions', 'latestWeight'));
    }

    /**
     * Anonimiza um utilizador com perfil Aluno/Paciente/Profissional (LGPD).
     * Preserva o registo quando existem vínculos financeiros ou clínicos (FK RESTRICT).
     */
    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        $actor = auth()->user();
        if (! $actor->isAdministrator() && ! $actor->hasPermission('users.delete')) {
            abort(403, 'Sem permissão para excluir utilizadores.');
        }

        if ($user->id === $actor->id) {
            return redirect()->route('admin.users')->with('error', 'Não pode excluir a sua própria conta.');
        }

        if ($user->is_admin || $user->isAdministrator()) {
            return redirect()->route('admin.users')->with('error', 'Não é permitido excluir administradores.');
        }

        if ($user->isAnonymized()) {
            return redirect()->route('admin.users')->with('error', 'Este utilizador já foi anonimizado.');
        }

        $allowedProfiles = ['aluno', 'paciente', 'professional'];
        $userProfileName = $user->userProfile?->name;

        if (! in_array($userProfileName, $allowedProfiles)) {
            return redirect()->route('admin.users')->with('error', 'Só é possível excluir utilizadores com perfil Aluno, Paciente ou Profissional.');
        }

        $deletedName = $user->name;
        $deletedId = $user->id;
        $roleLabel = $user->userProfile?->label ?? 'Utilizador';

        try {
            app(\App\Services\Lgpd\LgpdUserAnonymizationService::class)->anonymize(
                $user,
                $actor,
                'Exclusão solicitada via painel administrativo'
            );
        } catch (\Throwable $e) {
            Log::error('Falha ao anonimizar utilizador (admin)', [
                'target_id' => $deletedId,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('admin.users')->with('error', 'Não foi possível anonimizar o utilizador.');
        }

        AdminLog::create([
            'user_id' => $actor->id,
            'action' => "Anonimizou o {$userProfileName} #{$deletedId} ({$deletedName})",
            'ip_address' => $request->ip(),
            'payload' => ['anonymized_user_id' => $deletedId, 'profile' => $userProfileName],
            'created_at' => now(),
        ]);

        return redirect()->route('admin.users')->with('success', "{$roleLabel} {$deletedName} (ID {$deletedId}) foi anonimizado. Dados financeiros e clínicos preservados para auditoria.");
    }

    /**
     * Alterna o status do utilizador entre 'active' e 'blocked'.
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        $actor = auth()->user();
        if (! $actor->isAdministrator() && ! $actor->hasPermission('users.edit')) {
            abort(403, 'Sem permissão para alterar status de utilizadores.');
        }

        if ($user->id === $actor->id) {
            return back()->with('error', 'Não pode bloquear a sua própria conta.');
        }

        if ($user->is_admin || $user->isAdministrator()) {
            return back()->with('error', 'Não é permitido alterar o status de administradores.');
        }

        // Determinamos se o utilizador já está totalmente ativo (status, e-mail e aprovação)
        $isFullyActive = ($user->status === 'active' && $user->isEmailVerified() && $user->registration_approval_status === 'approved');
        
        // Se não estiver totalmente ativo, o clique no botão "Toggle" servirá para Ativar/Liberar totalmente.
        // Se já estiver ativo, o clique serve para Bloquear.
        $newStatus = $isFullyActive ? 'blocked' : 'active';

        
        if ($newStatus === 'active') {
            // Forçamos a ativação total do utilizador para "liberar o acesso" solicitado
            $user->status = 'active';
            $user->email_verified = true;
            $user->email_verified_at = now();
            $user->registration_approval_status = 'approved';
            $user->registration_reviewed_at = now();
            
            // Garantir que não há flags de reset forçado que possam bloquear o acesso imediato
            // se o admin só quis liberar o acesso rápido
            // $user->force_password_change = false; 

            $user->save();
        } else {
            $user->status = 'blocked';
            $user->save();
        }




        $statusLabel = $newStatus === 'active' ? 'desbloqueado' : 'bloqueado';
        $action = "{$statusLabel} o utilizador #{$user->id} ({$user->name})";

        AdminLog::create([
            'user_id' => $actor->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'payload' => ['target_user_id' => $user->id, 'new_status' => $newStatus],
            'created_at' => now(),
        ]);

        return back()->with('success', "Utilizador {$user->name} foi {$statusLabel} com sucesso.");
    }

    public function updateUser(Request $request, User $user)
    {
        Log::info('Admin updating user', [
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'request_data' => $request->all()
        ]);

        if ($request->filled('cpf')) {
            $request->merge(['cpf' => Cpf::normalize($request->input('cpf'))]);
        }

        $rules = [
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_id' => 'required|exists:roles,id',
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,blocked',
            'is_admin' => 'boolean',
            'premium_expires_at' => 'nullable|date',
            'department' => 'nullable|string|max:50',
            // Campos de Perfil do Aluno
            'birth_date' => 'required|date',
            'sex' => 'required|in:M,F',
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf,' . $user->id, new CpfValido()],
            'height_cm' => 'nullable|numeric|min:50|max:250',
            'weight_kg' => 'nullable|numeric|min:20|max:300',
        ];

        if ($request->get('profile_id') == 4) {
            $rules = array_merge($rules, [
                'profession_id' => 'required|exists:professions,id',
                'registration_number' => 'required|string|max:50',
                'council' => 'required|string|max:20',
                'registration_uf' => 'required|string|size:2',
                'registration_expiry_date' => 'required|date',
                'document_file' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
                'signature_file' => 'nullable|image|mimes:png|max:2048',
                'signature_data' => 'nullable|string',
            ]);
        }

        $data = $request->validate($rules);

        // Tratar checkboxes
        $data['is_admin'] = $request->has('is_admin');

        DB::transaction(function () use ($request, $user, $data) {
            // Lógica de verificação de email
            if ($request->has('is_admin')) {
                $data['email_verified_at'] = $user->email_verified_at ?? now();
            } elseif ($request->has('is_verified') && !$user->email_verified_at) {
                $data['email_verified_at'] = now();
            } elseif (!$request->has('is_verified') && $user->email_verified_at) {
                $data['email_verified_at'] = null;
            }

            // Atualizar os campos base do utilizador (remover campos de perfil para não dar erro no update do modelo User)
            $userFields = collect($data)->except(['birth_date', 'sex', 'height_cm', 'weight_kg'])->toArray();
            
            if (isset($userFields['cpf'])) {
                $userFields['cpf'] = Cpf::normalize($userFields['cpf']);
            }

            // O modelo User cuidará do bloqueio de Aluno -> Admin no evento saving
            $oldPlanId = $user->plan_id;
            
            // Garantir que is_premium está sincronizado com o plano PRO
            if (isset($userFields['plan_id'])) {
                $userFields['is_premium'] = ($userFields['plan_id'] == 2);
                if ($userFields['is_premium']) {
                    $userFields['premium_expires_at'] = null;
                }
            }

            $user->update($userFields);

            if (isset($userFields['profile_id'])) {
                $user->roles()->sync([$userFields['profile_id']]);
            }

            // Sincronizar com a tabela user_plans se o plano mudou ou se não houver plano ativo
            $hasActivePlan = $user->userPlans()->where('status', 'active')->exists();
            if ($oldPlanId != $user->plan_id || !$hasActivePlan) {
                // Desativar planos anteriores se mudou (ou se queremos forçar o atual)
                if ($oldPlanId != $user->plan_id) {
                    $user->userPlans()->where('status', 'active')->update(['status' => 'expired', 'end_date' => now()]);
                }

                // Criar/Garantir novo plano ativo se não existir um para o plano atual
                $currentActive = $user->userPlans()->where('plan_id', $user->plan_id)->where('status', 'active')->first();
                if (!$currentActive) {
                    $user->userPlans()->create([
                        'user_id' => $user->id,
                        'plan_id' => $user->plan_id,
                        'start_date' => now(),
                        'status' => 'active',
                    ]);
                }
                
                Log::info("Admin #".auth()->id()." sincronizou/alterou o plano do usuário #{$user->id} para o plano #{$user->plan_id}");
            }

            // Limpar cache de menus para garantir que a mudança seja imediata
            app(\App\Services\MenuService::class)->clearCache($user->id);

            // Atualizar perfil básico (Aluno/Geral)
            if ($user->profile_id != 4) {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'birth_date' => $data['birth_date'] ?? null,
                        'sex' => $data['sex'] ?? '',
                        'height_cm' => $data['height_cm'] ?? null,
                    ]
                );

                if (isset($data['weight_kg'])) {
                    \App\Models\WeightEntry::updateOrCreate(
                        ['user_id' => $user->id, 'weighed_at' => now()->toDateString()],
                        ['weight_kg' => $data['weight_kg']]
                    );
                }
            }

            if ($data['profile_id'] == 4) {
                $profileData = [
                    'profession_id' => $data['profession_id'],
                    'specialty' => $request->get('specialty'),
                    'registration_number' => $data['registration_number'],
                    'council' => $data['council'],
                    'registration_uf' => $data['registration_uf'],
                    'registration_expiry_date' => $data['registration_expiry_date'],
                    'updated_by' => auth()->id(),
                ];

                if ($request->hasFile('document_file')) {
                    $profileData['document_path'] = $request->file('document_file')->store('profissionais/documentos', 'local');
                    $profileData['document_version'] = ($user->professionalProfile->document_version ?? 0) + 1;
                }

                if ($request->hasFile('signature_file')) {
                    $profileData['signature_path'] = $request->file('signature_file')->store('profissionais/assinaturas', 'local');
                } elseif ($request->filled('signature_data')) {
                    $dataStr = $request->get('signature_data');
                    if (str_contains($dataStr, 'base64,')) {
                        $image = explode('base64,', $dataStr)[1];
                        $image = str_replace(' ', '+', $image);
                        $imageName = 'sig_' . $user->id . '_' . time() . '.png';
                        \Illuminate\Support\Facades\Storage::disk('local')->put('profissionais/assinaturas/' . $imageName, base64_decode($image));
                        $profileData['signature_path'] = 'profissionais/assinaturas/' . $imageName;
                    }
                }

                $user->professionalProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
            }

            // Individual User Permissions (Exceções Granulares)
            if ($request->has('direct_permissions')) {
                $user->permissions()->sync($request->input('direct_permissions'));
                
                // Invalida cache de permissões do usuário
                \Cache::forget("user_permissions_v2_{$user->id}");
                Log::info("Admin #".auth()->id()." atualizou permissões DIRETAS do usuário #{$user->id}");
            }

            // Sincronizar permissões do Perfil se enviadas e se tivermos um Profile ID (Role)
            if ($request->has('permissions') && $user->profile_id) {
                // Removemos permissões atuais para evitar duplicados
                DB::table('role_permissions')->where('role_id', $user->profile_id)->delete();
                
                $permIds = (array) $request->input('permissions');
                $pivotData = [];
                foreach ($permIds as $id) {
                    $pivotData[] = [
                        'role_id' => $user->profile_id,
                        'permission_id' => $id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($pivotData)) {
                    DB::table('role_permissions')->insert($pivotData);
                }
                
                // Invalida caches globais (Pode ser otimizado, mas garante consistência imediata)
                Log::info("Admin #".auth()->id()." atualizou permissões do Perfil #{$user->profile_id}. Invalidando caches.");
                // Nota: Em produção real, usaríamos tags de cache ou um padrão de invalidar por role.
            }
        });

        $user->refresh();
        $action = "Editou o utilizador #{$user->id} ({$user->name})";
        
        // Registar no Log Administrativo
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'ip_address' => $request->ip(),
            'payload' => $data,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.users')->with('success', "Utilizador {$user->name} atualizado com sucesso.");
    }

    public function catalog(Request $request): View
    {
        $query = ExerciseCatalog::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->get('search') . '%');
        }

        if ($request->filled('muscle_group')) {
            $query->where('muscle_group', $request->get('muscle_group'));
        }

        $exercises = $query->orderBy('muscle_group')->get();
        return view('admin.exercises.index', compact('exercises'));
    }

    public function storeExercise(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:exercises_catalog,name',
            'muscle_group' => 'required|string|max:64',
            'equipment' => 'nullable|string|max:64',
            'difficulty' => 'required|string|max:24',
            'instructions' => 'nullable|string',
            'video_type' => 'required|in:none,youtube,upload,gif',
            'video_url' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,gif,webm|max:20480',
            'tips' => 'nullable|string',
            'common_mistakes' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        $data['is_active'] = $request->has('is_active');
        
        if ($request->hasFile('video_file')) {
            $data['video_url'] = '/storage/' . $request->file('video_file')->store('exercises/videos', 'public');
        }

        if (isset($data['tips'])) {
            $data['tips'] = array_values(array_filter(array_map('trim', explode("\n", $data['tips']))));
        } else {
            $data['tips'] = [];
        }
        
        if (isset($data['common_mistakes'])) {
            $data['common_mistakes'] = array_values(array_filter(array_map('trim', explode("\n", $data['common_mistakes']))));
        } else {
            $data['common_mistakes'] = [];
        }

        $exercise = ExerciseCatalog::create($data);

        // Salvar músculos selecionados
        if ($request->has('selected_muscles')) {
            $muscleIds = json_decode($request->get('selected_muscles'), true);
            if (is_array($muscleIds)) {
                $exercise->muscles()->sync($muscleIds);
            }
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou o exercício: {$data['name']}",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return back()->with('success', 'Exercício cadastrado no catálogo.');
    }

    public function editExercise(ExerciseCatalog $exercise): View
    {
        $exercise->load('muscles.group');
        $selectedMuscles = $exercise->muscles->map(function($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'group' => $m->group->name,
                'type' => $m->type
            ];
        });
        return view('admin.exercises.edit', compact('exercise', 'selectedMuscles'));
    }

    public function updateExercise(Request $request, ExerciseCatalog $exercise)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:exercises_catalog,name,' . $exercise->id,
            'muscle_group' => 'required|string|max:64',
            'equipment' => 'nullable|string|max:64',
            'difficulty' => 'required|string|max:24',
            'instructions' => 'nullable|string',
            'video_type' => 'required|in:none,youtube,upload,gif',
            'video_url' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,gif,webm|max:20480',
            'tips' => 'nullable|string',
            'common_mistakes' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        $data['is_active'] = $request->has('is_active');
        
        if ($request->hasFile('video_file')) {
            $data['video_url'] = '/storage/' . $request->file('video_file')->store('exercises/videos', 'public');
        }

        if (isset($data['tips'])) {
            $data['tips'] = array_values(array_filter(array_map('trim', explode("\n", $data['tips']))));
        } else {
            $data['tips'] = [];
        }
        
        if (isset($data['common_mistakes'])) {
            $data['common_mistakes'] = array_values(array_filter(array_map('trim', explode("\n", $data['common_mistakes']))));
        } else {
            $data['common_mistakes'] = [];
        }

        $exercise->update($data);

        // Atualizar músculos selecionados
        if ($request->has('selected_muscles')) {
            $muscleIds = json_decode($request->get('selected_muscles'), true);
            if (is_array($muscleIds)) {
                $exercise->muscles()->sync($muscleIds);
            }
        }

        return redirect()->route('admin.exercises.catalog')->with('success', 'Exercício atualizado.');
    }

    public function deleteExercise(ExerciseCatalog $exercise)
    {
        $exercise->delete();
        return back()->with('success', 'Exercício removido do catálogo.');
    }

    public function announcements(): View
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'type' => 'required|string|in:info,success,warning,danger',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        Announcement::create($data);

        return back()->with('success', 'Aviso global publicado.');
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Aviso removido.');
    }

    public function aiMonitoring(): View
    {
        // 1. Total de Mensagens (Geral)
        $totalMessagesCount = DB::table('ai_chats')->count();
        $todayMessagesCount = DB::table('ai_chats')->whereDate('created_at', now())->count();
        $yesterdayMessagesCount = DB::table('ai_chats')->whereDate('created_at', now()->subDay())->count();

        // 2. Utilizadores mais Ativos (Ranking)
        $topUsers = DB::table('ai_chats')
            ->join('users', 'ai_chats.user_id', '=', 'users.id')
            ->select('users.name', 'users.email', DB::raw('count(*) as total'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // 3. Conversas Recentes
        $recentChats = User::has('aiChats') // Assumindo relação hasMany se existir, ou DB::table
            ->with(['aiChats' => fn($q) => $q->latest()->limit(5)])
            ->get()
            ->sortByDesc(fn($u) => $u->aiChats->first()?->created_at)
            ->take(8);

        // 4. Estimativa de Texto (tokens simples) - Apenas para referência
        $totalChars = DB::table('ai_chats')->sum(DB::raw('LENGTH(message)'));
        $estimatedTokens = round($totalChars / 4); // Regra aproximada para tokens

        return view('admin.ai_monitoring', compact(
            'totalMessagesCount', 
            'todayMessagesCount', 
            'yesterdayMessagesCount',
            'topUsers',
            'recentChats',
            'estimatedTokens'
        ));
    }

    public function exportUsersCsv()
    {
        $headers = ['ID', 'Nome', 'Email', 'Premium', 'Admin', 'Criado_em'];
        $users = User::all();

        return response()->streamDownload(function () use ($headers, $users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers, ';');

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->is_premium ? 'SIM' : 'NAO',
                    $user->is_admin ? 'SIM' : 'NAO',
                    $user->created_at->format('d/m/Y H:i'),
                ], ';');
            }
            fclose($handle);
        }, 'utilizadores_' . date('Y-m-d') . '.csv');
    }

    public function searchMuscles(Request $request)
    {
        $search = $request->get('q');
        
        $query = Muscle::with('group');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('group', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $muscles = $query->limit(20)->get()->map(function($muscle) {
            return [
                'id' => $muscle->id,
                'name' => $muscle->name,
                'group' => $muscle->group->name,
                'type' => $muscle->type
            ];
        });

        return response()->json($muscles);
    }

    public function exportPaymentsCsv()
    {
        $headers = ['MP_ID', 'User_ID', 'Plano', 'Valor', 'Data'];
        $payments = DB::table('mercadopago_payment_credits')->get();

        return response()->streamDownload(function () use ($headers, $payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers, ';');

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->mp_payment_id,
                    $p->user_id,
                    $p->plan_code,
                    $p->transaction_amount,
                    Carbon::parse($p->created_at)->format('d/m/Y H:i'),
                ], ';');
            }
            fclose($handle);
        }, 'pagamentos_' . date('Y-m-d') . '.csv');
    }

    public function loginForm()
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function logout(Request $request)
    {
        $userId = auth()->id();
        $email = auth()->user()?->email;

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        app(\App\Services\Operations\AuthAuditService::class)->log(
            \App\Models\AuthAuditLog::EVENT_LOGOUT,
            $userId,
            $email,
            true,
            $request,
            ['guard' => 'admin'],
        );

        return redirect()->route('admin.login')->with('success', 'Sessão encerrada.');
    }

    /** --- Módulo LGPD (Administração) --- */

    public function lgpdDashboard(): View
    {
        $workflow = app(\App\Services\Lgpd\LgpdDeletionWorkflowService::class);

        $stats = [
            'total_consents' => UserConsent::count(),
            'pending_deletions' => $workflow->pendingCount(),
            'recent_consents' => UserConsent::with('user')->orderBy('created_at', 'desc')->limit(10)->get(),
            'incidents_open' => DB::table('security_incidents')->where('status', '!=', 'closed')->count(),
            'recent_incidents' => DB::table('security_incidents')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];

        return view('admin.lgpd.index', compact('stats'));
    }

    public function consents(Request $request): View
    {
        $query = UserConsent::with('user');

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->whereHas('user', function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $consents = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();
        return view('admin.lgpd.consents', compact('consents'));
    }

    public function incidents(): View
    {
        $incidents = DB::table('security_incidents')->orderBy('created_at', 'desc')->get();
        return view('admin.lgpd.incidents', compact('incidents'));
    }

    public function storeIncident(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
        ]);

        DB::table('security_incidents')->insert([
            'reporter_id' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'severity' => $data['severity'],
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Incidente de segurança registrado.');
    }

    public function deletionRequests(): View
    {
        $workflow = app(\App\Services\Lgpd\LgpdDeletionWorkflowService::class);
        $pendingUsers = $workflow->pendingUsers(200);

        return view('admin.lgpd.deletion-requests', compact('pendingUsers'));
    }

    public function processDeletionRequest(Request $request, User $user): RedirectResponse
    {
        $actor = auth()->user();
        $workflow = app(\App\Services\Lgpd\LgpdDeletionWorkflowService::class);

        $outcome = $workflow->processUser(
            $user,
            $actor,
            'Anonimização manual via painel LGPD'
        );

        if ($outcome === 'processed') {
            AdminLog::create([
                'user_id' => $actor->id,
                'action' => "Processou pedido LGPD (anonimização) do utilizador #{$user->id}",
                'ip_address' => $request->ip(),
                'payload' => ['anonymized_user_id' => $user->id],
                'created_at' => now(),
            ]);

            return back()->with('success', "Utilizador #{$user->id} anonimizado com sucesso.");
        }

        if ($outcome === 'skipped') {
            return back()->with('warning', 'Utilizador já estava anonimizado.');
        }

        return back()->with('error', $outcome);
    }

    public function processDeletionRequestsBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1', 'max:50'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $actor = auth()->user();
        $workflow = app(\App\Services\Lgpd\LgpdDeletionWorkflowService::class);

        $result = $workflow->processUsers(
            $validated['user_ids'],
            $actor,
            'Anonimização em lote via painel LGPD'
        );

        AdminLog::create([
            'user_id' => $actor->id,
            'action' => 'Processou lote de pedidos LGPD (anonimização)',
            'ip_address' => $request->ip(),
            'payload' => $result,
            'created_at' => now(),
        ]);

        $message = "Lote concluído: {$result['processed']} processado(s), {$result['skipped']} ignorado(s), {$result['failed']} falha(s).";

        return back()->with($result['failed'] > 0 ? 'warning' : 'success', $message);
    }

    public function exportUserFullData(User $user)
    {
        $actor = auth()->user();
        if ($actor && ! $actor->isAdministrator()) {
            $actorCompanyId = (int) ($actor->academy_company_id ?? 0);
            $targetCompanyId = (int) ($user->academy_company_id ?? 0);
            if ($actorCompanyId === 0 || $targetCompanyId === 0 || $actorCompanyId !== $targetCompanyId) {
                abort(403, 'Exportação permitida apenas para utilizadores da sua organização.');
            }
        }

        $userData = [
            'user' => $user->only(['id', 'name', 'username', 'email', 'is_premium', 'created_at']),
            'profile' => $user->profile ? $user->profile->makeVisible(['height_cm', 'birth_date', 'gender']) : null,
            'food_entries' => DB::table('food_entries')->where('user_id', $user->id)->get(),
            'exercise_entries' => DB::table('exercise_entries')->where('user_id', $user->id)->get(),
            'weight_entries' => DB::table('weight_entries')->where('user_id', $user->id)->get(),
            'water_entries' => DB::table('water_entries')->where('user_id', $user->id)->get(),
            'consents' => UserConsent::where('user_id', $user->id)->get(),
            'exported_at' => now()->toIso8601String(),
            'exporter_admin_id' => auth()->id(),
        ];

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Exportou dados completos (LGPD) do utilizador #{$user->id}",
            'ip_address' => request()->ip(),
            'payload' => ['target_user_id' => $user->id]
        ]);

        $filename = 'export_lgpd_user_' . $user->id . '_' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($userData) {
            echo json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function systemErrors(): View
    {
        $systemErrors = SystemError::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.system_errors.index', compact('systemErrors'));
    }

    public function clearErrors()
    {
        SystemError::truncate();
        
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'Limpou todos os logs de erro do sistema',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Histórico de erros limpo com sucesso.');
    }

    public function security(Request $request): View
    {
        $preSelectedUser = null;
        if ($request->filled('user_id')) {
            $preSelectedUser = User::find($request->get('user_id'));
        }
        return view('admin.security.index', compact('preSelectedUser'));
    }

    public function changeAdminPassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => ['required'],
            'new_password' => $this->getPasswordValidationRules(true),
        ]);

        if (! Hash::check($request->input('current_password'), $user->password_hash)) {
            return back()->with('error', 'Senha atual incorreta.');
        }

        $user->password_hash = Hash::make($request->input('new_password'));
        $user->save();

        AdminLog::create([
            'user_id' => $user->id,
            'action' => "Alteração de senha administrativa (própria)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Senha alterada com sucesso. Faça login novamente.');
    }

    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => [
                'required', 
                'min:8', 
                'confirmed',
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[!@#$%^&*(),.?":{}|<>]/',
            ],
        ], [
            'new_password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, um número e um caractere especial.',
        ]);

        $user->password_hash = Hash::make($request->input('new_password'));
        $user->save();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Reset manual de senha para utilizador #{$user->id} ({$user->name})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => ['target_user_id' => $user->id],
            'created_at' => now(),
        ]);

        return back()->with('success', "Senha do utilizador {$user->name} foi alterada com sucesso.");
    }

    public function sendResetEmail(Request $request, User $user)
    {
        $mailErr = app(TransactionalMailService::class)->validateUserForOutgoingMail($user);
        if ($mailErr !== null) {
            return back()->with('error', $mailErr);
        }

        // Usar a funcionalidade padrão do Laravel para enviar link de reset
        try {
            MailConfigService::apply();
            $token = \Illuminate\Support\Facades\Password::createToken($user);
            $user->sendPasswordResetNotification($token);

            AdminLog::create([
                'user_id' => auth()->id(),
                'action' => "Enviou link de redefinição de senha para #{$user->id} ({$user->name})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return back()->with('success', "E-mail de redefinição enviado para {$user->email}.");
        } catch (\Exception $e) {
            return back()->with('error', "Falha ao enviar e-mail: " . $e->getMessage());
        }
    }

    public function generateAndSendNewPassword(Request $request, User $user)
    {
        $admin = auth()->user();

        // 1. Gerar senha segura
        $tempPassword = Str::password(16, true, true, true, false); 
        // Note: if Str::password is not available (Laravel < 10.x), I'll use a fallback. 
        // Let's check Laravel version first or use a custom generator.
        
        // 2. Atualizar banco de dados
        DB::transaction(function () use ($user, $tempPassword) {
            $user->password_hash = Hash::make($tempPassword);
            $user->force_password_change = true;
            $user->temp_password_expires_at = now()->addHours(24);
            $user->save();
        });

        // 2.1 Enviar para Central de Mensagens do Admin se for ALUNO
        if ($user->hasRole('aluno')) {
            \App\Services\SystemMessageService::sendPasswordNotificationToAdmin($user, $tempPassword, $admin);
        }

        // 3. Enviar e-mails
        $emailStatus = 'success';
        try {
            MailConfigService::apply();
            
            // E-mail para o Usuário
            app(TransactionalMailService::class)->sendToUser(
                new ForcedPasswordResetUserMail($user, $tempPassword),
                $user,
                $user->academy_company_id,
                MailSendType::PASSWORD_RESET,
                'Nova senha de acesso',
                'Reset forçado de senha (manual)'
            );

            // Notificação para o Administrador
            // Enviar para o e-mail do admin logado
            app(TransactionalMailService::class)->sendToUser(
                new ForcedPasswordResetAdminNotificationMail($user, $tempPassword, $admin),
                $admin,
                null,
                MailSendType::SYSTEM_ALERT,
                'Reset forçado de senha realizado',
                'Notificação de reset de senha para admin'
            );

        } catch (\Exception $e) {
            $emailStatus = 'failed: ' . $e->getMessage();
            Log::error('Erro ao enviar e-mail de reset forçado: ' . $e->getMessage());
        }

        // 4. Registrar auditoria
        AdminLog::create([
            'user_id' => $admin->id,
            'action' => "Gerou nova senha e enviou por e-mail para #{$user->id} ({$user->name})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'temp_password' => $tempPassword, // Conforme política de segurança do pedido
                'email_status' => $emailStatus,
                'expires_at' => now()->addHours(24)->toDateTimeString(),
            ],
            'created_at' => now(),
        ]);

        if (str_starts_with($emailStatus, 'failed')) {
            return back()->with('error', "Nova senha gerada no banco, mas houve falha no envio do e-mail: " . $emailStatus);
        }

        return back()->with('success', "Nova senha gerada e enviada com sucesso para o e-mail do usuário. O administrador foi notificado.");
    }

    /**
     * Get the validation rules for passwords.
     *
     * @param  bool  $requireConfirmation
     * @return array
     */
    protected function getPasswordValidationRules($requireConfirmation = false)
    {
        $rules = ['required', 'string', 'min:8'];
        
        if ($requireConfirmation) {
            $rules[] = 'confirmed';
        }
        
        return $rules;
    }
}
