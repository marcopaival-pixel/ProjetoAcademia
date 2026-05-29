@extends('layouts.admin')

@section('title', 'Cadastrar Funcionário da Clínica')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('admin.registrations.index') }}" class="inline-flex items-center gap-3 text-zinc-500 hover:text-white transition-all group">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center group-hover:bg-amber-500/20 group-hover:text-amber-400 transition-all">
                <i class="fas fa-chevron-left text-sm"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest">Voltar para o Menu</span>
        </a>
    </div>

    <form action="{{ route('admin.registrations.funcionario-clinica.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Sessão: Perfil e Identidade -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/5 blur-3xl -mr-32 -mt-32"></div>
            
            <div class="flex items-center gap-6 mb-8">
                <div class="relative group">
                    <div class="w-24 h-24 bg-zinc-800 rounded-2xl border-2 border-dashed border-white/10 flex items-center justify-center overflow-hidden group-hover:border-amber-500/50 transition-all cursor-pointer">
                        <i class="fas fa-camera text-zinc-500 text-2xl group-hover:scale-110 transition-transform"></i>
                        <input type="file" name="avatar" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-bold text-xl">Foto Administrativa</h3>
                    <p class="text-zinc-500 text-sm">Identificação do funcionário para o sistema.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Nome Completo</label>
                    <input type="text" name="name" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="Nome do funcionário">
                </div>
                
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">E-mail</label>
                    <input type="email" name="email" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="email@clinica.com">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">CPF</label>
                    <input type="text" name="cpf" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="000.000.000-00">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">WhatsApp / Telefone</label>
                    <input type="text" name="whatsapp" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="(00) 00000-0000">
                </div>
            </div>
        </div>

        <!-- Sessão: Dados Administrativos -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-id-card-alt text-amber-500"></i> Informações do Cargo
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Clínica</label>
                    @if($myCompany)
                        <input type="hidden" name="academy_company_id" value="{{ $myCompany->id }}">
                        <div class="w-full bg-amber-500/10 border border-amber-500/20 rounded-xl px-4 py-3 text-amber-400 font-bold">
                            {{ $myCompany->name }}
                        </div>
                    @else
                        <select name="academy_company_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all">
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Cargo</label>
                    <select name="clinic_role" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all">
                        <option value="Recepcionista">Recepcionista</option>
                        <option value="Secretária">Secretária</option>
                        <option value="Financeiro">Financeiro</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Gerente">Gerente</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Setor</label>
                    <select name="sector" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all">
                        <option value="Recepção">Recepção</option>
                        <option value="Financeiro">Financeiro</option>
                        <option value="Administração">Administração</option>
                        <option value="Atendimento">Atendimento</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tipo de Vínculo</label>
                    <select name="link_type" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all">
                        <option value="CLT">CLT</option>
                        <option value="PJ">PJ</option>
                        <option value="Estágio">Estágio</option>
                        <option value="Terceirizado">Terceirizado</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Data de Admissão</label>
                    <input type="date" name="admission_date" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-amber-500/50 outline-none transition-all">
                </div>
            </div>

            <div class="mt-8">
                <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest mb-4 block">Permissões do Sistema</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-amber-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="paciente_cadastro" checked class="rounded border-white/10 bg-zinc-800 text-amber-500">
                        <span class="text-zinc-300 text-sm">Pode cadastrar pacientes</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-amber-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="agenda_gerenciar" checked class="rounded border-white/10 bg-zinc-800 text-amber-500">
                        <span class="text-zinc-300 text-sm">Pode gerenciar agendas</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-amber-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="prontuario_ver" class="rounded border-white/10 bg-zinc-800 text-amber-500">
                        <span class="text-zinc-300 text-sm">Pode visualizar prontuários</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-amber-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="financeiro_ver" class="rounded border-white/10 bg-zinc-800 text-amber-500">
                        <span class="text-zinc-300 text-sm">Pode visualizar financeiro</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.registrations.index') }}" class="px-8 py-4 text-zinc-500 font-bold hover:text-zinc-300 transition-all">Cancelar</a>
            <button type="submit" class="px-10 py-4 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/20 transition-all flex items-center gap-3">
                Finalizar Cadastro <i class="fas fa-check"></i>
            </button>
        </div>
    </form>
</div>
@endsection
