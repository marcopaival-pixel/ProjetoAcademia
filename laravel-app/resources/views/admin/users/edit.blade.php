@extends('layouts.admin')

@section('title', 'Editar Utilizador: ' . $user->name)

@section('content')
<div class="space-y-10 animate-fade-in max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Ficha do Utilizador <span class="text-blue-500">#{{ $user->id }}</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Gestão de acessos, perfil e conformidade LGPD.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-white/5 hover:text-white transition-all shadow-xl">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
    </div>

    <!-- Main Configuration Form -->
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label for="name" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700">
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700">
                </div>
            </div>

            <div class="p-8 bg-zinc-950/40 border border-white/5 rounded-[2rem] space-y-8">
                <h3 class="text-lg font-black text-white tracking-tight italic border-b border-white/5 pb-4">Nível de Acesso & Assinatura</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Premium Toggle -->
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <div class="relative flex items-center justify-center mt-1">
                            <input type="checkbox" name="is_premium" value="1" {{ $user->is_premium ? 'checked' : '' }} class="peer sr-only">
                            <div class="w-6 h-6 rounded-lg bg-zinc-900 border border-white/10 peer-checked:bg-blue-600 peer-checked:border-blue-500 transition-colors flex items-center justify-center">
                                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-white group-hover:text-blue-400 transition-colors">Utilizador Premium Ativo</span>
                            <span class="text-[10px] text-zinc-500 font-medium uppercase tracking-widest mt-1 block">Dá acesso ao chat IA ilimitado e relatórios.</span>
                        </div>
                    </label>

                    <!-- Admin Toggle -->
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <div class="relative flex items-center justify-center mt-1">
                            <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} class="peer sr-only">
                            <div class="w-6 h-6 rounded-lg bg-zinc-900 border border-white/10 peer-checked:bg-red-600 peer-checked:border-red-500 transition-colors flex items-center justify-center">
                                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-red-500 group-hover:text-red-400 transition-colors">Acesso Root (Administrador)</span>
                            <span class="text-[10px] text-zinc-500 font-medium uppercase tracking-widest mt-1 block">Permite gerir toda a plataforma no painel admin.</span>
                        </div>
                    </label>
                </div>

                <div class="space-y-2 pt-4 border-t border-white/5">
                    <label for="premium_expires_at" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Expiração do Premium</label>
                    <input type="date" id="premium_expires_at" name="premium_expires_at" value="{{ $user->premium_expires_at ? $user->premium_expires_at->format('Y-m-d') : '' }}"
                        class="w-full md:w-1/2 bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700">
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight mt-2 px-1">Deixe vazio para premium vitalício (se flag ativa).</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-4 pt-4">
                <button type="submit" class="w-full md:w-auto flex-1 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/10">
                    Atualizar Cadastro
                </button>
                <a href="{{ route('admin.users') }}" class="w-full md:w-auto py-5 px-8 bg-zinc-900/50 border border-white/5 text-zinc-400 font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:text-white hover:bg-white/5 transition-all text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Additional Information & LGPD -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Account Info -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-xl">
            <h3 class="text-lg font-black text-white tracking-tight italic mb-6">Informações de Conta</h3>
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4 bg-zinc-950/50 p-4 rounded-2xl border border-white/5">
                    <div class="w-10 h-10 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-500">
                        <i class="fas fa-calendar-alt text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest">Membro desde</span>
                        <span class="text-sm font-bold text-white mt-1">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 bg-zinc-950/50 p-4 rounded-2xl border border-white/5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600/10 flex items-center justify-center text-emerald-500">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest">Última Atualização</span>
                        <span class="text-sm font-bold text-white mt-1">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'Nunca' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- LGPD & Compliance -->
        <div class="bg-gradient-to-br from-indigo-900/20 to-blue-900/10 backdrop-blur-3xl border border-blue-500/20 p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
            
            <div class="relative z-10 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-lg font-black text-white tracking-tight italic">LGPD & Conformidade</h3>
                </div>
                
                <p class="text-zinc-400 text-xs leading-relaxed mb-8 flex-grow">
                    Direito à Portabilidade: É possível exportar todos os dados deste utilizador, incluindo configurações, em formato estruturado JSON de acordo com a legislação.
                </p>
                
                <a href="{{ route('admin.lgpd.export-user', $user->id) }}" class="flex items-center justify-between w-full p-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/20">
                    <span>Exportar Todos os Dados (JSON)</span>
                    <i class="fas fa-download"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
