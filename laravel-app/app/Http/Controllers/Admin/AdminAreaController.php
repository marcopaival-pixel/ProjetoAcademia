<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminLog;
use App\Models\SystemError;
use App\Models\AdminSetting;
use App\Models\ExerciseCatalog;
use App\Models\Announcement;
use App\Models\FoodEntry;
use App\Services\AdminOverviewStats;
use App\Models\UserConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAreaController extends Controller
{
    public function dashboard(): View
    {
        $overview = AdminOverviewStats::collect();
        
        // Métrica Financeira: Total de Créditos do Mercado Pago
        $totalRevenue = DB::table('mercadopago_payment_credits')->sum('transaction_amount');
        $activeSubscriptions = DB::table('mercadopago_subscriptions')->where('status', 'authorized')->get();
        
        // Cálculo de MRR (Monthly Recurring Revenue)
        $mrr = 0;
        foreach ($activeSubscriptions as $sub) {
            if ($sub->plan_code === 'monthly') {
                $mrr += 19.9;
            } elseif ($sub->plan_code === 'yearly') {
                $mrr += (149.9 / 12);
            }
        }

        // Assinaturas a expirar nos próximos 15 dias
        $expiringCount = User::where('is_premium', true)
            ->whereBetween('premium_expires_at', [now(), now()->addDays(15)])
            ->count();

        // Métrica de Perfil: Distribuição de Objetivos
        $goalsDistribution = DB::table('user_profiles')
            ->select('goal', DB::raw('count(*) as total'))
            ->groupBy('goal')
            ->get();

        // Comparativo de Faturamento: Mês Atual vs Mês Anterior
        $thisMonthRevenue = DB::table('mercadopago_payment_credits')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('transaction_amount');

        $lastMonthRevenue = DB::table('mercadopago_payment_credits')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('transaction_amount');

        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 100;

        $metrics = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'total_premium' => User::where('is_premium', true)->count(),
            'total_revenue' => $totalRevenue,
            'active_subs' => $activeSubscriptions->count(),
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
            'monthly_revenue' => DB::table('mercadopago_payment_credits')
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month, sum(transaction_amount) as total"))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get()
                ->reverse()
                ->values(),
        ];

        return view('admin.dashboard', compact('overview', 'metrics'));
    }

    public function users(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('premium')) {
            $query->where('is_premium', $request->get('premium') === 'yes');
        }

        if ($request->filled('admin')) {
            $query->where('is_admin', $request->get('admin') === 'yes');
        }

        $users = $query->orderByDesc('created_at')
            ->paginate(40)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'overview' => AdminOverviewStats::collect(),
        ]);
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
            'disk_free' => round(disk_free_space("/") / 1024 / 1024 / 1024, 2) . ' GB',
            'disk_total' => round(disk_total_space("/") / 1024 / 1024 / 1024, 2) . ' GB',
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
            AdminSetting::set($key, $value);
        }

        return back()->with('success', 'Configurações atualizadas.');
    }
    public function editUser(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_premium' => 'boolean',
            'is_admin' => 'boolean',
            'premium_expires_at' => 'nullable|date',
        ]);

        // Tratar checkboxes que não vêm no request quando desmarcados
        $data['is_premium'] = $request->has('is_premium');
        $data['is_admin'] = $request->has('is_admin');

        $user->update($data);

        // Registar no Log Administrativo
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Editou o utilizador #{$user->id} ({$user->name})",
            'ip_address' => $request->ip(),
            'payload' => $data
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
            'video_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        ExerciseCatalog::create($data);

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
        return view('admin.exercises.edit', compact('exercise'));
    }

    public function updateExercise(Request $request, ExerciseCatalog $exercise)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:exercises_catalog,name,' . $exercise->id,
            'muscle_group' => 'required|string|max:64',
            'equipment' => 'nullable|string|max:64',
            'difficulty' => 'required|string|max:24',
            'instructions' => 'nullable|string',
            'video_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $exercise->update($data);

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
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Sessão encerrada.');
    }

    /** --- Módulo LGPD (Administração) --- */

    public function lgpdDashboard(): View
    {
        $stats = [
            'total_consents' => UserConsent::count(),
            'recent_consents' => UserConsent::with('user')->orderBy('created_at', 'desc')->limit(10)->get(),
            'incidents_open' => DB::table('security_incidents')->where('status', '!=', 'closed')->count(),
            'recent_incidents' => DB::table('security_incidents')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];

        return view('admin.lgpd.index', compact('stats'));
    }

    public function consents(): View
    {
        $consents = UserConsent::with('user')->orderBy('created_at', 'desc')->paginate(50);
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

    public function exportUserFullData(User $user)
    {
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
        $errors = SystemError::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.system_errors.index', compact('errors'));
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
}
