@php
    $user = auth()->user();
    $redirectUrl = route('dashboard');
    
    if ($user) {
        if ($user->isAdministrator()) {
            $redirectUrl = route('admin.dashboard');
        } elseif ($user->isRegistrationPending()) {
            $redirectUrl = route('registration.pending');
        } elseif ($user->isRegistrationRejected()) {
            $redirectUrl = route('registration.rejected');
        } elseif ($user->hasRole('manager') || $user->hasRole('receptionist') || $user->hasRole('supervisor')) {
            $redirectUrl = route('admin.dashboard'); // Ou agenda.index, mas admin.dashboard é seguro
        } elseif ($user->hasRole('professional')) {
            $redirectUrl = route('professional.dashboard');
        } elseif ($user->hasRole('paciente') && !$user->hasRole('aluno')) {
            $redirectUrl = route('patient.unified.dashboard');
        } elseif ($user->onboarding_status === 'pending') {
            $redirectUrl = route('onboarding.welcome');
        }
    }
@endphp
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Conta Ativada — {{ config('app.name', 'NexShape') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/nexshape-icon.svg') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #0b0e14; font-family: 'Manrope', sans-serif; }
        
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        .animate-scale-in { animation: scaleIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .animate-checkmark { animation: checkmark 0.4s ease-out 0.4s both; }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out 0.4s both; }
        .animate-fade-in-up-delay-1 { animation: fadeInUp 0.6s ease-out 0.6s both; }
        .animate-fade-in-up-delay-2 { animation: fadeInUp 0.6s ease-out 0.8s both; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden bg-zinc-950 text-zinc-300 selection:bg-emerald-500/30">
    <!-- Ambient Background Glows -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-600/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-md w-full mx-auto px-6 py-12 relative z-10 flex flex-col items-center">
        <!-- Logo -->
        <div class="mb-8 text-center animate-fade-in-up">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-2xl shadow-emerald-500/20 overflow-hidden bg-zinc-900 border border-zinc-800 mx-auto">
                <img src="{{ asset('images/nexshape-icon.svg') }}" alt="NexShape" class="w-10 h-10 object-contain">
            </div>
        </div>

        <!-- Animated Check Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-500/20 border border-emerald-500/40 mb-6 shadow-[0_0_40px_rgba(16,185,129,0.3)] animate-scale-in relative">
            <div class="absolute inset-0 rounded-full border border-emerald-400/60 animate-ping opacity-20" style="animation-duration: 2s;"></div>
            <i class="fas fa-check text-3xl text-emerald-400 animate-checkmark"></i>
        </div>

        <!-- Texts -->
        <h1 class="text-3xl font-bold text-white tracking-tight mb-3 animate-fade-in-up text-center">Conta ativada!</h1>
        <p class="text-zinc-400 text-[15px] leading-relaxed text-center mb-8 animate-fade-in-up">
            Seu e-mail foi confirmado com sucesso.<br>
            Sua conta já está pronta para uso.
        </p>

        <!-- Divider -->
        <div class="w-full h-px bg-zinc-800/60 mb-8 animate-fade-in-up-delay-1"></div>

        <!-- Next Steps -->
        <div class="w-full mb-8 animate-fade-in-up-delay-1">
            <h3 class="text-zinc-100 font-semibold mb-5 text-sm uppercase tracking-wider text-center">Próximos passos</h3>
            <ul class="space-y-4 max-w-[280px] mx-auto">
                <li class="flex items-center gap-4 text-zinc-300 text-[15px]">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-[11px] text-emerald-400"></i>
                    </div>
                    Complete seu perfil
                </li>
                <li class="flex items-center gap-4 text-zinc-300 text-[15px]">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-[11px] text-emerald-400"></i>
                    </div>
                    Configure seus objetivos
                </li>
                <li class="flex items-center gap-4 text-zinc-300 text-[15px]">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-[11px] text-emerald-400"></i>
                    </div>
                    Explore os recursos do NexShape
                </li>
            </ul>
        </div>

        <!-- Divider -->
        <div class="w-full h-px bg-zinc-800/60 mb-8 animate-fade-in-up-delay-2"></div>

        <!-- Button -->
        <a href="{{ $redirectUrl }}" class="w-full inline-flex items-center justify-center py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl transition-all active:scale-[0.98] shadow-lg shadow-emerald-600/20 animate-fade-in-up-delay-2 text-[15px]">
            Acessar o NexShape
        </a>
        
        <a href="{{ route('login') }}" class="mt-5 text-zinc-500 hover:text-zinc-400 text-sm transition-colors font-medium animate-fade-in-up-delay-2 flex items-center justify-center">
            <i class="fas fa-arrow-left mr-2 text-[12px]"></i> Voltar ao Login
        </a>

        <!-- Footer -->
        <p class="mt-12 text-zinc-600 text-[13px] font-medium animate-fade-in-up-delay-2 text-center">
            Bem-vindo ao ecossistema NexShape.<br>
            Sua jornada de alta performance começa agora.
        </p>
    </div>
</body>
</html>
