@extends('layouts.admin')

@section('title', 'Cadastrar Paciente da Clínica')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('admin.registrations.index') }}" class="inline-flex items-center gap-3 text-zinc-500 hover:text-white transition-all group">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center group-hover:bg-rose-500/20 group-hover:text-rose-400 transition-all">
                <i class="fas fa-chevron-left text-sm"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest">Voltar para o Menu</span>
        </a>
    </div>

    <form action="{{ route('admin.registrations.paciente-clinica.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Sessão: Dados Pessoais -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/5 blur-3xl -mr-32 -mt-32"></div>
            
            <div class="flex items-center gap-6 mb-8">
                <div class="relative group">
                    <div class="w-24 h-24 bg-zinc-800 rounded-2xl border-2 border-dashed border-white/10 flex items-center justify-center overflow-hidden group-hover:border-rose-500/50 transition-all cursor-pointer">
                        <i class="fas fa-camera text-zinc-500 text-2xl group-hover:scale-110 transition-transform"></i>
                        <input type="file" name="avatar" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-bold text-xl">Identificação do Paciente</h3>
                    <p class="text-zinc-500 text-sm">Registro oficial da clínica.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Nome Completo</label>
                    <input type="text" name="name" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="Nome do paciente">
                </div>
                
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">E-mail</label>
                    <input type="email" name="email" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="paciente@clinica.com">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">CPF</label>
                    <input type="text" name="cpf" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="000.000.000-00">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">WhatsApp / Celular</label>
                    <input type="text" name="whatsapp" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="(00) 00000-0000">
                </div>
            </div>
        </div>

        <!-- Sessão: Dados Administrativos e Convênio -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-file-invoice text-rose-500"></i> Faturamento e Convênio
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Clínica / Unidade</label>
                    @if($myCompany)
                        <div class="w-full bg-rose-500/10 border border-rose-500/20 rounded-xl px-4 py-3 text-rose-400 font-bold">
                            {{ $myCompany->name }}
                        </div>
                    @else
                        <select name="academy_company_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all">
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Plano / Convênio</label>
                    <select name="insurance_type" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all">
                        <option value="Particular">Particular</option>
                        <option value="Plano de Saúde">Plano de Saúde</option>
                        <option value="Empresa">Empresa / Convênio</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Número da Carteirinha</label>
                    <input type="text" name="insurance_card_number" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all" placeholder="Se aplicável">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Validade do Plano</label>
                    <input type="date" name="insurance_expiry" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all">
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Responsável Legal (para menores)</label>
                    <input type="text" name="responsible_legal" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all" placeholder="Nome completo do responsável">
                </div>
            </div>
        </div>

        <!-- Sessão: Dados de Saúde -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-notes-medical text-rose-500"></i> Histórico de Saúde
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tipo Sanguíneo</label>
                    <select name="blood_type" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all">
                        <option value="">Não informado</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2 md:col-span-3">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Alergias / Restrições</label>
                    <textarea name="allergy_details" rows="2" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all" placeholder="Liste alergias relevantes..."></textarea>
                </div>

                <div class="space-y-2 md:col-span-3">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Histórico Clínico / Observações</label>
                    <textarea name="medical_history" rows="3" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all" placeholder="Antecedentes cirúrgicos, patológicos, etc..."></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.registrations.index') }}" class="px-8 py-4 text-zinc-500 font-bold hover:text-zinc-300 transition-all">Cancelar</a>
            <button type="submit" class="px-10 py-4 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/20 transition-all flex items-center gap-3">
                Finalizar Registro <i class="fas fa-check"></i>
            </button>
        </div>
    </form>
</div>
@endsection
