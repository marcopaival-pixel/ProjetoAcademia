@php
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#238636');
    $loggedIn = auth()->check();
    $customLogo = \App\Models\AdminSetting::get('logo_url', '');
@endphp
<!DOCTYPE html>
<html lang="pt-PT" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-layout.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/sidebar-toggle.js') }}" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cyber: {
                            blue: '#3b82f6',
                            emerald: '#10b981',
                            indigo: '#8b5cf6',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --accent: {{ $accentColor }};
            --accent-dim: {{ $accentColor }}cc;
        }
        body {
            background-color: #0b0e14;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-container">
        @include('partials.sidebar')

        <div class="main-area">
            @include('partials.topbar')

            @if(session('success'))
                <div class="p-4 m-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <main id="main" class="content-wrapper">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold">@yield('title')</h1>
                </div>
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
