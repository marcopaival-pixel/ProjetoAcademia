@php
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#238636');
@endphp
<!DOCTYPE html>
<html lang="pt-PT" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0a0c10;
            --bg-sidebar: #11141b;
            --bg-card: #161b22;
            --border-color: #30363d;
            --text-main: #e6edf3;
            --text-muted: #8b949e;
            --accent: {{ $accentColor }};
            --accent-hover: {{ $accentColor }}cc;
            --danger: #f85149;
            --sidebar-width: 260px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        aside {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            border-right: 1px border var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
        }

        nav {
            flex: 1;
            padding: 1.5rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-item {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-item:hover, .nav-item.active {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .nav-item.active {
            color: #fff;
            background-color: var(--accent);
        }

        /* Main Content */
        main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 2rem 3rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.875rem;
            margin: 0;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success { background-color: rgba(35, 134, 54, 0.2); color: #3fb950; }
        .badge-info { background-color: rgba(56, 139, 253, 0.2); color: #58a6ff; }
        .badge-warning { background-color: rgba(210, 153, 34, 0.2); color: #d29922; }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-block;
            text-decoration: none;
        }

        .btn-primary { background-color: var(--accent); color: white; }
        .btn-primary:hover { background-color: var(--accent-hover); }

        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.875rem; }
        input, select, textarea {
            width: 100%;
            background-color: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.75rem;
            color: white;
            font-family: inherit;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
        .alert-success { background-color: rgba(63, 185, 80, 0.1); border: 1px solid rgba(63, 185, 80, 0.2); color: #3fb950; }

        @media (max-width: 768px) {
            aside { display: none; }
            main { margin-left: 0; padding: 1.5rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside>
        <div class="sidebar-header">
            ProjetoAcademia
        </div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <span>🏠</span> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="nav-item {{ Route::is('admin.users') ? 'active' : '' }}">
                <span>👥</span> Usuários
            </a>
            <a href="{{ route('admin.logs') }}" class="nav-item {{ Route::is('admin.logs') ? 'active' : '' }}">
                <span>📝</span> Logs
            </a>
            <a href="{{ route('admin.exercises.catalog') }}" class="nav-item {{ Route::is('admin.exercises.catalog') ? 'active' : '' }}">
                <span>🏋️</span> Catálogo Exercícios
            </a>
            <a href="{{ route('admin.announcements') }}" class="nav-item {{ Route::is('admin.announcements') ? 'active' : '' }}">
                <span>📢</span> Avisos Globais
            </a>
            <a href="{{ route('admin.ai.monitoring') }}" class="nav-item {{ Route::is('admin.ai.monitoring') ? 'active' : '' }}">
                <span>🤖</span> IA Insights
            </a>
            <a href="{{ route('admin.monitoring') }}" class="nav-item {{ Route::is('admin.monitoring') ? 'active' : '' }}">
                <span>📊</span> Monitoramento
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-item {{ Route::is('admin.settings') ? 'active' : '' }}">
                <span>⚙️</span> Configurações
            </a>
            <div style="margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <a href="{{ url('/') }}" class="nav-item">
                    <span>⬅️</span> Voltar ao Site
                </a>
                <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0; padding: 0;">
                    @csrf
                    <button type="submit" class="nav-item" style="background: none; border: none; width: 100%; cursor: pointer;">
                        <span>🚪</span> Sair da Gestão
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <main>
        <header>
            <h1>@yield('title')</h1>
            <div>
                <span>Admin: {{ auth()->user()->name }}</span>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
