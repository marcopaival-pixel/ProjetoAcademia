<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Implantação de Clínica — @yield('title', 'Wizard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #09090b;
        }
        .step-active { color: #3b82f6; }
        .step-completed { color: #10b981; }
        .glass-panel {
            background: rgba(24, 24, 27, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .progress-bar {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%);
            transition: width 0.5s ease-in-out;
        }
        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="text-zinc-300 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-zinc-950/50 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fas fa-clinic-medical text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg leading-none">Implantação NexShape</h1>
                    <p class="text-zinc-500 text-xs mt-1">{{ $company->name }}</p>
                </div>
            </div>
            
            <div class="hidden md:flex items-center space-x-2">
                @foreach($steps as $num => $s)
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 
                            {{ $step == $num ? 'border-blue-500 text-blue-500 bg-blue-500/10' : ($step > $num ? 'border-emerald-500 text-emerald-500 bg-emerald-500/10' : 'border-zinc-800 text-zinc-600') }}">
                            @if($step > $num)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        @if($num < 11)
                            <div class="w-4 h-0.5 {{ $step > $num ? 'bg-emerald-500/50' : 'bg-zinc-800' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div>
                <a href="{{ route('admin.pdf-companies.index') }}" class="text-zinc-500 hover:text-white transition-colors text-sm font-medium">
                    <i class="fas fa-times mr-2"></i>Sair
                </a>
            </div>
        </div>
        <!-- Mobile Progress Bar -->
        <div class="progress-bar w-full">
            <div class="progress-fill" style="width: {{ ($step / 11) * 100 }}%"></div>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-12 px-6">
        <div class="max-w-4xl w-full">
            <div class="glass-panel rounded-3xl p-8 md:p-12 shadow-2xl animate-fade-up">
                @if(session('success'))
                    <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl flex items-center">
                        <i class="fas fa-check-circle mr-3"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-8 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="mb-10">
                    <span class="text-blue-500 font-bold tracking-widest text-xs uppercase">Etapa {{ str_pad($step, 2, '0', STR_PAD_LEFT) }}/11</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-white mt-2">{{ $currentStep['title'] }}</h2>
                </div>

                @yield('content')
            </div>

            <!-- Footer Info -->
            <div class="mt-8 flex justify-between items-center text-zinc-500 text-xs px-4">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt mr-2"></i> Ambiente Seguro de Implantação
                </div>
                <div>
                    NexShape v2.5
                </div>
            </div>
        </div>
    </main>

    @stack('scripts')
    @include('partials.js-masks')
</body>
</html>
