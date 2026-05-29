@extends('layouts.admin')

@section('title', 'Cadastrar Profissional da Clínica')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('admin.registrations.index') }}" class="inline-flex items-center gap-3 text-zinc-500 hover:text-white transition-all group">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center group-hover:bg-indigo-500/20 group-hover:text-indigo-400 transition-all">
                <i class="fas fa-chevron-left text-sm"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest">Voltar para o Menu</span>
        </a>
    </div>

    <form action="{{ route('admin.registrations.professional-clinica.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Sessão: Perfil e Identidade -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 blur-3xl -mr-32 -mt-32"></div>
            
            <div class="flex items-center gap-6 mb-8">
                <div class="relative group">
                    <div class="w-24 h-24 bg-zinc-800 rounded-2xl border-2 border-dashed border-white/10 flex items-center justify-center overflow-hidden group-hover:border-indigo-500/50 transition-all cursor-pointer">
                        <i class="fas fa-camera text-zinc-500 text-2xl group-hover:scale-110 transition-transform"></i>
                        <input type="file" name="avatar" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-bold text-xl">Foto Profissional</h3>
                    <p class="text-zinc-500 text-sm">Identificação do profissional na clínica.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Nome Completo</label>
                    <input type="text" name="name" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="Nome do profissional">
                </div>
                
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">E-mail Corporativo</label>
                    <input type="email" name="email" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="email@clinica.com">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">CPF</label>
                    <input type="text" name="cpf" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="000.000.000-00">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">WhatsApp / Telefone</label>
                    <input type="text" name="whatsapp" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="(00) 00000-0000">
                </div>
            </div>
        </div>

        <!-- Sessão: Vínculo Institucional -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-hospital text-indigo-500"></i> Vínculo com a Clínica
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Clínica (Matriz)</label>
                    @if($myCompany)
                        <input type="hidden" name="academy_company_id" value="{{ $myCompany->id }}">
                        <div class="w-full bg-indigo-500/10 border border-indigo-500/20 rounded-xl px-4 py-3 text-indigo-400 font-bold">
                            {{ $myCompany->name }}
                        </div>
                    @else
                        <select name="academy_company_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all">
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Unidade / Filial</label>
                    <select name="academy_unit_id" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all">
                        <option value="">Selecione a Unidade</option>
                        @if($myCompany)
                            @foreach($myCompany->units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Cargo na Instituição</label>
                    <select name="clinic_role" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all">
                        <option value="Profissional">Profissional</option>
                        <option value="Coordenador">Coordenador</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Responsável Técnico">Responsável Técnico</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tipo de Vínculo</label>
                    <select name="link_type" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all">
                        <option value="Funcionário">Funcionário (CLT)</option>
                        <option value="Prestador de Serviço">Prestador de Serviço (PJ)</option>
                        <option value="Parceiro">Parceiro</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sessão: Dados Técnicos e Especialidade -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-stethoscope text-indigo-500"></i> Dados Técnicos
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Profissão</label>
                    <select name="profession_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all">
                        @foreach($professions as $profession)
                            <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Registro Profissional</label>
                    <input type="text" name="registration_number" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all" placeholder="Ex: CRM-123456">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Sala de Atendimento</label>
                    <input type="text" name="room" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all" placeholder="Ex: Consultório 04">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tempo Padrão de Consulta (min)</label>
                    <input type="number" name="appointment_duration" value="30" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500/50 outline-none transition-all">
                </div>
            </div>

            <div class="mt-8">
                <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest mb-4 block">Permissões de Acesso</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-indigo-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="laudo" checked class="rounded border-white/10 bg-zinc-800 text-indigo-500">
                        <span class="text-zinc-300 text-sm">Pode emitir laudos</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-indigo-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="prontuario" checked class="rounded border-white/10 bg-zinc-800 text-indigo-500">
                        <span class="text-zinc-300 text-sm">Pode editar prontuários</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-indigo-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="relatorio" class="rounded border-white/10 bg-zinc-800 text-indigo-500">
                        <span class="text-zinc-300 text-sm">Pode visualizar relatórios clínicos</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 bg-black/10 border border-white/5 rounded-2xl cursor-pointer hover:border-indigo-500/30 transition-all">
                        <input type="checkbox" name="internal_permissions[]" value="agenda" checked class="rounded border-white/10 bg-zinc-800 text-indigo-500">
                        <span class="text-zinc-300 text-sm">Pode gerenciar própria agenda</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.registrations.index') }}" class="px-8 py-4 text-zinc-500 font-bold hover:text-zinc-300 transition-all">Cancelar</a>
            <button type="submit" class="px-10 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-3">
                Finalizar Cadastro <i class="fas fa-check"></i>
            </button>
        </div>
    </form>
</div>
@endsection
