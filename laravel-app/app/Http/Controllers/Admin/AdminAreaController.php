<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminLog;
use App\Models\AdminSetting;
use App\Models\ExerciseCatalog;
use App\Models\Announcement;
use App\Models\FoodEntry;
use App\Services\AdminOverviewStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAreaController extends Controller
{
    public function dashboard(): View
    {
        $overview = AdminOverviewStats::collect();
        
        // Métrica Financeira: Total de Créditos do Mercado Pago
        $totalRevenue = \Illuminate\Support\Facades\DB::table('mercadopago_payment_credits')->sum('transaction_amount');
        $activeSubscriptions = \Illuminate\Support\Facades\DB::table('mercadopago_subscriptions')->where('status', 'authorized')->count();

        // Métrica de Perfil: Distribuição de Objetivos
        $goalsDistribution = \Illuminate\Support\Facades\DB::table('user_profiles')
            ->select('goal', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('goal')
            ->get();

        $metrics = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'total_premium' => User::where('is_premium', true)->count(),
            'total_revenue' => $totalRevenue,
            'active_subs' => $activeSubscriptions,
            'goals' => $goalsDistribution,
            'recent_users' => User::orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_logs' => AdminLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];

        return view('admin.dashboard', compact('overview', 'metrics'));
    }

    public function users(): View
    {
        $users = User::query()
            ->orderByDesc('created_at')
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

    public function catalog(): View
    {
        $exercises = ExerciseCatalog::orderBy('muscle_group')->get();
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
        ]);

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
        ]);

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
        $totalMessagesCount = \Illuminate\Support\Facades\DB::table('ai_chats')->count();
        $todayMessagesCount = \Illuminate\Support\Facades\DB::table('ai_chats')->whereDate('created_at', now())->count();
        $yesterdayMessagesCount = \Illuminate\Support\Facades\DB::table('ai_chats')->whereDate('created_at', now()->subDay())->count();

        // 2. Utilizadores mais Ativos (Ranking)
        $topUsers = \Illuminate\Support\Facades\DB::table('ai_chats')
            ->join('users', 'ai_chats.user_id', '=', 'users.id')
            ->select('users.name', 'users.email', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // 3. Conversas Recentes
        $recentChats = \App\Models\User::has('aiChats') // Assumindo relação hasMany se existir, ou DB::table
            ->with(['aiChats' => fn($q) => $q->latest()->limit(5)])
            ->get()
            ->sortByDesc(fn($u) => $u->aiChats->first()?->created_at)
            ->take(8);

        // 4. Estimativa de Texto (tokens simples) - Apenas para referência
        $totalChars = \Illuminate\Support\Facades\DB::table('ai_chats')->sum(\Illuminate\Support\Facades\DB::raw('LENGTH(message)'));
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
        $payments = \Illuminate\Support\Facades\DB::table('mercadopago_payment_credits')->get();

        return response()->streamDownload(function () use ($headers, $payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers, ';');

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->mp_payment_id,
                    $p->user_id,
                    $p->plan_code,
                    $p->transaction_amount,
                    \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i'),
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
}
