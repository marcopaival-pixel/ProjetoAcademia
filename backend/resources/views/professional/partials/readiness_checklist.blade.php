<div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-10 shadow-2xl overflow-hidden relative group/ready">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-emerald-500/5 opacity-50 group-hover/ready:opacity-100 transition-opacity"></div>
    
    <div class="relative z-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-black text-white leading-none">Preparação para Uso</h3>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] mt-2">Status de Configuração da Conta</p>
            </div>
            <div class="text-right">
                <span class="text-4xl font-black text-white">{{ $readiness['percentage'] }}%</span>
                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Concluído</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="h-4 bg-zinc-950 rounded-full border border-white/5 overflow-hidden mb-10 shadow-inner">
            <div class="h-full bg-gradient-to-r from-blue-600 via-indigo-500 to-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all duration-1000 ease-out" style="width: {{ $readiness['percentage'] }}%"></div>
        </div>

        @if($readiness['is_ready'])
            <div class="p-8 bg-emerald-500/10 border border-emerald-500/20 rounded-[2.5rem] text-center mb-6">
                <div class="w-16 h-16 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-check text-2xl"></i>
                </div>
                <h4 class="text-xl font-black text-white">Conta Pronta para Uso!</h4>
                <p class="text-zinc-400 text-sm mt-2">Todas as configurações essenciais foram concluídas com sucesso.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($readiness['items'] as $key => $item)
                    <div class="flex items-center justify-between p-5 bg-white/5 rounded-3xl border border-white/5 hover:bg-white/10 transition-all group/item">
                        <div class="flex items-center gap-5">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all shadow-lg {{ $item['status'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20' : 'bg-zinc-800 text-zinc-600 border border-white/5' }}">
                                <i class="fas {{ $item['status'] ? 'fa-check-circle' : 'fa-circle' }} text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black {{ $item['status'] ? 'text-white' : 'text-zinc-400' }}">{{ $item['label'] }}</h4>
                                <p class="text-[10px] text-zinc-500 font-medium">{{ $item['description'] }}</p>
                            </div>
                        </div>
                        
                        @if(!$item['status'])
                            <a href="{{ $item['route'] }}" class="px-5 py-2.5 bg-blue-600/10 text-blue-400 text-[10px] font-black rounded-xl border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all">
                                CORRIGIR
                            </a>
                        @else
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest px-4 py-2 bg-emerald-500/5 rounded-xl">OK</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-10 pt-8 border-t border-white/5 flex items-center justify-between text-zinc-600">
            <span class="text-[9px] font-black uppercase tracking-widest">
                {{ $readiness['completed_count'] }} de {{ $readiness['total_count'] }} itens validados
            </span>
            <i class="fas fa-shield-halved text-lg opacity-20"></i>
        </div>
    </div>
</div>
