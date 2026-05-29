@extends('layouts.admin')

@section('title', 'Cadastrar Profissional Único')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('admin.registrations.index') }}" class="inline-flex items-center gap-3 text-zinc-500 hover:text-white transition-all group">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center group-hover:bg-blue-500/20 group-hover:text-blue-400 transition-all">
                <i class="fas fa-chevron-left text-sm"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest">Voltar para o Menu</span>
        </a>
    </div>

    <form action="{{ route('admin.registrations.professional-unico.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Sessão: Perfil e Identidade -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 blur-3xl -mr-32 -mt-32"></div>
            
            <div class="flex items-center gap-6 mb-8">
                <div class="relative group">
                    <div class="w-24 h-24 bg-zinc-800 rounded-2xl border-2 border-dashed border-white/10 flex items-center justify-center overflow-hidden group-hover:border-blue-500/50 transition-all cursor-pointer">
                        <i class="fas fa-camera text-zinc-500 text-2xl group-hover:scale-110 transition-transform"></i>
                        <input type="file" name="avatar" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-bold text-xl">Foto do Perfil</h3>
                    <p class="text-zinc-500 text-sm">JPG, PNG ou WebP. Máx 2MB.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Nome Completo</label>
                    <input type="text" name="name" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="Digite o nome completo">
                </div>
                
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">E-mail de Acesso</label>
                    <input type="email" name="email" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="email@exemplo.com">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">CPF</label>
                    <input type="text" name="cpf" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="000.000.000-00">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">WhatsApp</label>
                    <input type="text" name="whatsapp" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="(00) 00000-0000">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Data de Nascimento</label>
                    <input type="date" name="birth_date" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Sexo</label>
                    <select name="sex" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                        <option value="O">Outro</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sessão: Dados Profissionais -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-briefcase text-blue-500"></i> Especialização e Atuação
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Profissão</label>
                    <select name="profession_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                        @foreach($professions as $profession)
                            <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Número do Registro (CRM/CRN/CREF)</label>
                    <input type="text" name="registration_number" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all" placeholder="Ex: 12345/SP">
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Especialidades</label>
                    <input type="text" name="specialty" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="Ex: Nutrição Esportiva, Emagrecimento">
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Formação Acadêmica</label>
                    <textarea name="education" rows="2" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all" placeholder="Detalhes da graduação e pós-graduação"></textarea>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Certificações Extras</label>
                    <textarea name="certifications" rows="2" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all" placeholder="Cursos, congressos e especializações"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tipo de Atendimento</label>
                    <select name="service_types" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                        <option value="presencial">Presencial</option>
                        <option value="online">Online</option>
                        <option value="hibrido">Híbrido</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Status Inicial</label>
                    <select name="status" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                        <option value="suspended">Suspenso</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sessão: Agenda e Plano -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-calendar-alt text-blue-500"></i> Configuração Operacional
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Plano de Assinatura</label>
                    <select name="plan_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Início do Expediente</label>
                    <input type="time" name="work_start_time" value="08:00" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Fim do Expediente</label>
                    <input type="time" name="work_end_time" value="18:00" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tempo de Consulta (min)</label>
                    <input type="number" name="appointment_duration" value="60" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all">
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Dias de Atendimento</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'] as $day)
                        <label class="flex items-center gap-2 px-4 py-2 bg-black/20 border border-white/5 rounded-xl cursor-pointer hover:border-blue-500/30 transition-all">
                            <input type="checkbox" name="work_days[]" value="{{ $day }}" checked class="rounded border-white/10 bg-zinc-800 text-blue-500 focus:ring-0">
                            <span class="text-zinc-300 text-sm font-medium">{{ $day }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
             <div class="space-y-2">
                <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Senha Provisória</label>
                <input type="password" name="password" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="Mínimo 8 caracteres">
                <p class="text-zinc-600 text-xs">O profissional poderá alterar a senha em seu primeiro acesso.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.registrations.index') }}" class="px-8 py-4 text-zinc-500 font-bold hover:text-zinc-300 transition-all">Cancelar</a>
            <button type="submit" class="px-10 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/20 transition-all flex items-center gap-3">
                Finalizar Cadastro <i class="fas fa-check"></i>
            </button>
        </div>
    </form>
</div>
@endsection
