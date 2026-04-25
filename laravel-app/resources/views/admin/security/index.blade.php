@extends('layouts.admin')

@section('title', 'Segurança e Controle de Acessos')

@section('content')
<div class="space-y-10 animate-fade-in max-w-6xl mx-auto">
    <!-- Header Context -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Cofre de Segurança</h2>
            <p class="text-zinc-500 text-sm mt-1">Gestão de senhas e políticas de proteção de dados NexShape.</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
            <i class="fas fa-user-shield text-xl"></i>
        </div>
    </div>

    @if(session('error'))
        <div class="p-5 bg-red-500/10 border border-red-500/20 text-red-500 rounded-2xl flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        <!-- CARD: ALTERAR MINHA SENHA -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl space-y-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-500">
                    <i class="fas fa-key"></i>
                </div>
                <h3 class="text-xl font-black text-white italic">Alterar Minha Senha</h3>
            </div>

            <form action="{{ route('admin.security.change-password') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label for="current_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha Atual</label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="new_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nova Senha</label>
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    </div>
                </div>

                <div class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                    <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i> A senha deve ter no mínimo 8 caracteres, 
                        contendo letra maiúscula, número e caractere especial.
                    </p>
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                    Salvar Nova Senha
                </button>
            </form>
        </div>

        <!-- CARD: RESETAR SENHA DE USUÁRIO -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl space-y-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-600/10 flex items-center justify-center text-amber-500">
                    <i class="fas fa-user-lock"></i>
                </div>
                <h3 class="text-xl font-black text-white italic">Gestão de Senhas Externas</h3>
            </div>

            <div class="space-y-10">
                <!-- Ação 1: Enviar link via Email -->
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Opção 1: Enviar Link de Recuperação</h4>
                    <p class="text-xs text-zinc-600">O utilizador receberá um e-mail com instruções para redefinir a própria senha com segurança.</p>
                    
                    <form action="#" id="form-send-reset" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Buscar Utilizador</label>
                            <input type="text" id="user-search" placeholder="Digite nome ou e-mail para buscar..." 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-amber-600 transition-all">
                            <div id="search-results" class="bg-zinc-950 border border-white/5 rounded-2xl max-h-40 overflow-y-auto hidden"></div>
                            <input type="hidden" name="selected_user_id" id="selected-user-id-reset">
                        </div>

                        <button type="submit" id="btn-submit-reset" disabled class="w-full py-4 bg-zinc-950 border border-white/10 text-zinc-500 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-amber-600 hover:text-white transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                            Enviar Redefinição de Senha
                        </button>
                    </form>
                </div>

                <div class="border-t border-white/5 pt-8 space-y-4">
                    <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Opção 2: Reset Forçado (Manual)</h4>
                    <p class="text-xs text-zinc-600">Altere a senha do utilizador imediatamente. Recomendado apenas para casos de suporte crítico.</p>
                    
                    <form action="#" id="form-force-reset" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="force_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nova Senha</label>
                                <input type="password" id="force_password" name="new_password" required
                                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-red-600 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label for="force_password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar</label>
                                <input type="password" id="force_password_confirmation" name="new_password_confirmation" required
                                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-red-600 transition-all">
                            </div>
                        </div>
                        <input type="hidden" name="selected_user_id" id="selected-user-id-force">

                        <button type="submit" id="btn-submit-force" disabled class="w-full py-4 bg-red-600/10 text-red-500 font-black text-[10px] uppercase tracking-[0.2em] border border-red-500/20 rounded-2xl hover:bg-red-600 hover:text-white transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                            Forçar Troca de Senha
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const userSearch = document.getElementById('user-search');
    const searchResults = document.getElementById('search-results');
    const selectedUserIdReset = document.getElementById('selected-user-id-reset');
    const selectedUserIdForce = document.getElementById('selected-user-id-force');
    const btnReset = document.getElementById('btn-submit-reset');
    const btnForce = document.getElementById('btn-submit-force');
    const formReset = document.getElementById('form-send-reset');
    const formForce = document.getElementById('form-force-reset');

    let debounceTimer;

    // Handle pre-selected user from URL
    const urlParams = new URLSearchParams(window.location.search);
    const preSelectedUserId = urlParams.get('user_id');
    if (preSelectedUserId) {
        fetch(`{{ route('admin.users') }}?search=${preSelectedUserId}&ajax=1`)
            .then(res => res.json())
            .then(data => {
                if (data.users.length > 0) {
                    const user = data.users.find(u => u.id == preSelectedUserId) || data.users[0];
                    selectUser(user);
                }
            });
    }

    userSearch.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value;

        if (query.length < 3) {
            searchResults.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('admin.users') }}?search=${encodeURIComponent(query)}&ajax=1`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.users.length === 0) {
                        searchResults.innerHTML = '<div class="p-4 text-zinc-500 text-xs italic">Nenhum utilizador encontrado.</div>';
                    } else {
                        data.users.forEach(user => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-white/5 cursor-pointer border-b border-white/5 transition-colors flex justify-between items-center';
                            div.innerHTML = `
                                <div>
                                    <div class="text-white text-xs font-black">${user.name}</div>
                                    <div class="text-[10px] text-zinc-500">${user.email}</div>
                                </div>
                                <span class="text-[9px] bg-zinc-900 text-zinc-400 px-2 py-0.5 rounded uppercase font-black">Selecionar</span>
                            `;
                            div.onclick = () => selectUser(user);
                            searchResults.appendChild(div);
                        });
                    }
                    searchResults.classList.remove('hidden');
                });
        }, 300);
    });

    function selectUser(user) {
        userSearch.value = `${user.name} (${user.email})`;
        selectedUserIdReset.value = user.id;
        selectedUserIdForce.value = user.id;
        searchResults.classList.add('hidden');
        
        btnReset.disabled = false;
        btnReset.classList.remove('text-zinc-500');
        btnReset.classList.add('text-black', 'bg-amber-600');
        
        btnForce.disabled = false;
        btnForce.classList.remove('text-red-500');
        btnForce.classList.add('text-white', 'bg-red-600');

        formReset.action = `/admin/users/${user.id}/send-reset-link`;
        formForce.action = `/admin/users/${user.id}/reset-password`;
    }

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!userSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
</script>
@endpush

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
