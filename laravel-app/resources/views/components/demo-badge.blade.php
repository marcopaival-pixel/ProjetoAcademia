@if(session('is_demo_mode'))
<div class="fixed bottom-6 left-6 z-[9999] animate-bounce-slow" x-data="{ open: false, showPresentation: false }">
    <div class="relative">
        <!-- Floating Badge -->
        <button @click="open = !open" 
            class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl shadow-2xl hover:scale-105 transition-all border border-white/20 backdrop-blur-md">
            <div class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
            </div>
            <span class="font-bold tracking-tight">Modo Demonstração</span>
            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>

        <!-- Menu Expandido -->
        <div x-show="open" @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            class="absolute bottom-full mb-4 left-0 w-72 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-xl border border-white/20 dark:border-zinc-800 rounded-3xl shadow-2xl p-4 overflow-hidden">
            
            <div class="mb-4">
                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-1">Perfil Ativo</p>
                <div class="flex items-center gap-2 text-zinc-800 dark:text-zinc-100">
                    <span class="p-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    <span class="font-bold">{{ ucfirst(session('demo_profile', 'profissional')) }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <form action="{{ route('demo.switch') }}" method="POST">
                    @csrf
                    <p class="text-[10px] font-bold text-zinc-400 uppercase mb-3 tracking-widest">Trocar Visão de Experiência</p>
                    <div class="flex flex-col gap-3">
                        @php 
                            $currentProfile = session('demo_profile', 'professional');
                            if($currentProfile === 'student') $currentProfile = 'aluno';
                            if($currentProfile === 'clinic') $currentProfile = 'gestor';
                        @endphp

                        <!-- Perfil Aluno -->
                        <button name="profile" value="aluno" 
                            class="group relative flex items-center gap-4 p-3 rounded-[1.5rem] border transition-all {{ $currentProfile === 'aluno' ? 'bg-emerald-50 border-emerald-200 ring-2 ring-emerald-500/20 dark:bg-emerald-500/10 dark:border-emerald-500/40' : 'bg-white border-zinc-100 hover:border-emerald-200 dark:bg-zinc-800/50 dark:border-zinc-700 dark:hover:border-emerald-500/30' }}">
                            <div class="w-12 h-12 flex-shrink-0 rounded-2xl flex items-center justify-center {{ $currentProfile === 'aluno' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-400 group-hover:text-emerald-500' }} transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-black uppercase tracking-widest {{ $currentProfile === 'aluno' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400' }}">Visão Aluno</p>
                                <p class="text-[10px] text-zinc-400 font-medium leading-tight">Performance e Dieta</p>
                            </div>
                            @if($currentProfile === 'aluno')
                                <span class="absolute right-4 px-2 py-0.5 bg-emerald-500 text-[8px] font-black text-white uppercase rounded-full shadow-sm">Ativo</span>
                            @endif
                        </button>

                        <!-- Perfil Profissional -->
                        <button name="profile" value="professional" 
                            class="group relative flex items-center gap-4 p-3 rounded-[1.5rem] border transition-all {{ $currentProfile === 'professional' ? 'bg-indigo-50 border-indigo-200 ring-2 ring-indigo-500/20 dark:bg-indigo-500/10 dark:border-indigo-500/40' : 'bg-white border-zinc-100 hover:border-indigo-200 dark:bg-zinc-800/50 dark:border-zinc-700 dark:hover:border-indigo-500/30' }}">
                            <div class="w-12 h-12 flex-shrink-0 rounded-2xl flex items-center justify-center {{ $currentProfile === 'professional' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-400 group-hover:text-indigo-500' }} transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-black uppercase tracking-widest {{ $currentProfile === 'professional' ? 'text-indigo-600 dark:text-indigo-400' : 'text-zinc-500 dark:text-zinc-400' }}">Visão Pro</p>
                                <p class="text-[10px] text-zinc-400 font-medium leading-tight">Gestão e Prescrição</p>
                            </div>
                            @if($currentProfile === 'professional')
                                <span class="absolute right-4 px-2 py-0.5 bg-indigo-500 text-[8px] font-black text-white uppercase rounded-full shadow-sm">Ativo</span>
                            @endif
                        </button>


                    </div>
                </form>

                <div class="pt-4 mt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                    <!-- Botão de Apresentação -->
                    <button @click="showPresentation = true" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all text-sm font-black shadow-lg shadow-purple-500/20 uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path></svg>
                        Apresentação Interativa
                    </button>

                    <button onclick="window.startNexShapeTour()" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200/50 dark:border-indigo-500/20 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors text-sm font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Iniciar Tour Guiado
                    </button>

                    <form action="{{ route('demo.reset') }}" method="POST" class="inline-block w-full">
                        @csrf
                        <button class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Resetar Ambiente
                        </button>
                    </form>

                    <a href="{{ route('demo.stop') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-colors text-sm font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Sair da Demo
                    </a>

                    <div class="pt-2 grid grid-cols-2 gap-2">
                        <a href="{{ route('plano') }}" class="flex items-center justify-center gap-1 px-3 py-2 bg-emerald-500 text-zinc-950 rounded-xl text-[10px] font-black uppercase tracking-tighter hover:bg-emerald-400 transition-colors">
                            Assinar Plano
                        </a>
                        <a href="https://wa.me/5500000000000" target="_blank" class="flex items-center justify-center gap-1 px-3 py-2 bg-zinc-900 text-white border border-zinc-800 rounded-xl text-[10px] font-black uppercase tracking-tighter hover:bg-zinc-800 transition-colors">
                            Falar Vendas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <!-- Modal de Apresentação Interativa -->
    <template x-teleport="body">
        <div x-show="showPresentation" 
            class="fixed inset-0 z-[10000] flex items-center justify-center p-4 sm:p-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            
            <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-md" @click="showPresentation = false"></div>
            
            <div class="relative w-full max-w-4xl bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                <div class="flex flex-col md:flex-row h-full max-h-[90vh]">
                    <!-- Sidebar da Apresentação -->
                    <div class="w-full md:w-1/3 bg-gradient-to-br from-indigo-600 to-purple-700 p-8 text-white">
                        <div class="mb-8">
                            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mb-4 backdrop-blur-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h2 class="text-3xl font-black leading-tight uppercase tracking-tighter italic">NexShape<br>Platform</h2>
                            <p class="text-indigo-100 text-sm mt-4 font-medium opacity-80">A próxima geração em performance e gestão clínica.</p>
                        </div>

                        <nav class="space-y-4">
                            <div class="flex items-center gap-3 opacity-100">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                <span class="text-sm font-bold uppercase tracking-widest">Inovação</span>
                            </div>
                            <div class="flex items-center gap-3 opacity-50">
                                <div class="w-2 h-2 bg-white/40 rounded-full"></div>
                                <span class="text-sm font-bold uppercase tracking-widest">Tecnologia</span>
                            </div>
                            <div class="flex items-center gap-3 opacity-50">
                                <div class="w-2 h-2 bg-white/40 rounded-full"></div>
                                <span class="text-sm font-bold uppercase tracking-widest">Escalabilidade</span>
                            </div>
                        </nav>
                    </div>

                    <!-- Conteúdo Principal -->
                    <div class="flex-1 p-8 md:p-12 overflow-y-auto">
                        <button @click="showPresentation = false" class="absolute top-6 right-6 text-zinc-400 hover:text-zinc-600 dark:hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="space-y-8">
                            <div>
                                <h3 class="text-zinc-950 dark:text-white text-2xl font-black mb-4 uppercase tracking-tight italic">Transformando a Experiência Fitness</h3>
                                <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed font-medium">
                                    O NexShape não é apenas um software de gestão; é um ecossistema de inteligência artificial projetado para maximizar resultados de alunos e a produtividade de profissionais.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="p-5 bg-zinc-50 dark:bg-zinc-800/50 rounded-3xl border border-zinc-100 dark:border-zinc-700">
                                    <h4 class="text-indigo-600 dark:text-indigo-400 font-black text-xs uppercase mb-2">NexBot IA</h4>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Prescrições inteligentes e suporte técnico 24/7 para seus alunos.</p>
                                </div>
                                <div class="p-5 bg-zinc-50 dark:bg-zinc-800/50 rounded-3xl border border-zinc-100 dark:border-zinc-700">
                                    <h4 class="text-emerald-600 dark:text-emerald-400 font-black text-xs uppercase mb-2">NexNeural</h4>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Algoritmos que sugerem cargas e progressões baseados em ciência.</p>
                                </div>
                            </div>

                            <div class="bg-zinc-950 p-6 rounded-[2rem] text-white">
                                <h4 class="text-xs font-black uppercase text-zinc-500 mb-4 tracking-widest">Para Clínicas e Franquias</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-center gap-3 text-sm">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Visão macro de faturamento e retenção.
                                    </li>
                                    <li class="flex items-center gap-3 text-sm">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Gestão de múltiplos profissionais e unidades.
                                    </li>
                                    <li class="flex items-center gap-3 text-sm">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        White label com a identidade da sua marca.
                                    </li>
                                </ul>
                            </div>

                            <div class="pt-8 flex flex-col sm:flex-row gap-4">
                                <button @click="showPresentation = false; window.startNexShapeTour()" class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-500/20 uppercase tracking-wider text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Iniciar Tour Guiado
                                </button>
                                <button @click="showPresentation = false" class="flex-1 px-6 py-4 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold rounded-2xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all text-sm uppercase tracking-wider">
                                    Concluir Apresentação
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 3s infinite ease-in-out;
    }
</style>
@endif
