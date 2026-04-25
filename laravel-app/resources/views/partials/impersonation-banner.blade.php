@if(session()->has('impersonated_clinic_id'))
    @php
        $impersonatedClinic = \App\Models\AcademyCompany::find(session('impersonated_clinic_id'));
    @endphp
    @if($impersonatedClinic)
        <div class="bg-amber-600 text-white px-6 py-3 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-[100] shadow-lg animate-fade-in border-b border-amber-500/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center animate-pulse">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Modo administrativo ativo</p>
                    <p class="text-sm font-bold">Você está acessando a clínica: <span class="underline decoration-amber-400 decoration-2">{{ $impersonatedClinic->name }}</span></p>
                </div>
            </div>
            
            <form action="{{ route('admin.impersonate-clinic.stop') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-xl bg-white text-amber-700 text-[10px] font-black uppercase hover:bg-amber-50 transition-all shadow-sm flex items-center gap-2 group">
                    <i class="fas fa-sign-out-alt group-hover:translate-x-1 transition-transform"></i>
                    Sair do acesso da clínica
                </button>
            </form>
        </div>
    @endif
@endif
