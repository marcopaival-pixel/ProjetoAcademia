@extends('layouts.admin')

@section('title', 'Configuração de Chatbots')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/omnichannel.css') }}">
    <style>
        .bot-card { background: var(--omni-card); border: 1px solid var(--omni-border); border-radius: 20px; padding: 24px; margin-bottom: 24px; transition: all 0.3s; }
        .bot-card:hover { border-color: var(--accent); transform: translateY(-2px); }
        .step-item { border-left: 2px solid var(--accent); padding-left: 20px; margin-bottom: 20px; position: relative; }
        .step-item::before { content: ''; position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: var(--accent); }
        .badge-type { font-size: 10px; padding: 2px 8px; border-radius: 99px; background: rgba(var(--accent-rgb), 0.1); color: var(--accent); text-transform: uppercase; font-weight: 700; }
    </style>
@endpush

@section('content')
<div class="content-wrapper" x-data="{ 
    // Modais
    modalBot: false,
    modalStep: false,
    modalOption: false,
    
    // Dados
    editMode: false,
    currentBotId: null,
    currentStepId: null,
    
    botData: { id: null, name: '', whatsapp_phone: '', is_active: true, out_of_office_message: '' },
    stepData: { id: null, label: '', type: 'message', content: '', is_start: false, next_step_id: null },
    optionData: { id: null, trigger_value: '', label: '', destination_step_id: null },

    // Funções Bot
    openCreateBot() {
        this.editMode = false;
        this.botData = { id: null, name: '', whatsapp_phone: '', is_active: true, out_of_office_message: '' };
        this.modalBot = true;
    },
    openEditBot(bot) {
        this.editMode = true;
        this.botData = { ...bot, is_active: !!bot.is_active };
        this.modalBot = true;
    },

    // Funções Step
    openCreateStep(botId) {
        this.editMode = false;
        this.currentBotId = botId;
        this.stepData = { id: null, label: '', type: 'message', content: '', is_start: false, next_step_id: null };
        this.modalStep = true;
    },
    openEditStep(step) {
        this.editMode = true;
        this.stepData = { ...step, is_start: !!step.is_start };
        this.modalStep = true;
    },

    // Funções Option
    openCreateOption(stepId) {
        this.currentStepId = stepId;
        this.optionData = { id: null, trigger_value: '', label: '', destination_step_id: null };
        this.modalOption = true;
    }
}">
    <div class="flex justify-between items-center mb-10">
        <div class="animate-[fadeIn_0.5s_ease-out]">
            <h1 class="text-4xl font-black tracking-tighter text-white bg-clip-text text-transparent bg-gradient-to-r from-white to-white/50">
                Nexus Intelligence Center
            </h1>
            <p class="text-zinc-500 font-medium mt-1">Configure o fluxo de automação e IA para seus canais.</p>
        </div>
        <button @click="openCreateBot()" class="group relative px-8 py-3.5 bg-indigo-600 rounded-2xl font-bold text-white shadow-2xl shadow-indigo-600/30 transition-all hover:scale-105 active:scale-95 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
            <span class="flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Novo Agente Digital
            </span>
        </button>
    </div>

    @if($bots->count() == 0)
        <div class="relative group p-20 rounded-[3rem] border border-white/5 bg-zinc-900/30 backdrop-blur-3xl text-center overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
            <div class="relative">
                <div class="w-24 h-24 bg-zinc-800 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner ring-1 ring-white/10">
                    <i class="fas fa-robot text-4xl text-zinc-600 group-hover:text-indigo-400 transition-colors duration-500"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Vácuo de Inteligência Detectado</h2>
                <p class="text-zinc-500 mb-10 max-w-md mx-auto">Seu ecossistema ainda não possui agentes de automação. Inicie a configuração do seu primeiro bot agora.</p>
                <button @click="openCreateBot()" class="px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl text-white font-black uppercase tracking-widest text-[10px] transition-all">
                    Iniciar Implantação
                </button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($bots as $bot)
                <div class="relative group rounded-[2.5rem] border border-white/5 bg-zinc-900/40 p-8 backdrop-blur-2xl transition-all hover:border-indigo-500/30">
                    <!-- Bot Header -->
                    <div class="flex justify-between items-start mb-10">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 flex items-center justify-center ring-1 ring-indigo-500/20 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-500">
                                <i class="fas fa-robot text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white tracking-tight">{{ $bot->name }}</h3>
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span class="flex items-center gap-1.5 {{ $bot->is_active ? 'text-emerald-400' : 'text-rose-400' }} text-[10px] font-black uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $bot->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400' }}"></span>
                                        {{ $bot->is_active ? 'Operacional' : 'Offline' }}
                                    </span>
                                    <span class="text-zinc-600 text-[10px] font-black uppercase tracking-widest">WPP: {{ $bot->whatsapp_phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="openEditBot({{ json_encode($bot) }})" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-all"><i class="fas fa-cog text-xs text-zinc-400"></i></button>
                            <form action="{{ route('admin.omnichannel.bots.destroy', $bot) }}" method="POST" data-confirm-delete>
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all text-rose-500"><i class="fas fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </div>

                    <!-- Steps Timeline -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center px-2">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Roteiro de Atendimento</h4>
                            <button @click="openCreateStep({{ $bot->id }})" class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400 hover:text-indigo-300 transition-colors">
                                <i class="fas fa-plus mr-1"></i> Adicionar Passo
                            </button>
                        </div>

                        <div class="relative space-y-4 max-h-[400px] overflow-y-auto pr-2 scrollbar-hide">
                            @foreach($bot->steps as $step)
                                <div class="relative p-6 rounded-3xl bg-white/[0.02] border border-white/5 hover:border-white/10 transition-all group/step">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 text-[9px] font-black uppercase tracking-widest ring-1 ring-indigo-500/20">
                                                {{ $step->type }}
                                            </span>
                                            <span class="text-sm font-bold text-white">{{ $step->label }}</span>
                                            @if($step->is_start)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[8px] font-black uppercase tracking-widest ring-1 ring-emerald-500/20">Boot</span>
                                            @endif
                                        </div>
                                        <div class="flex gap-1 opacity-0 group-hover/step:opacity-100 transition-opacity">
                                            <button @click="openEditStep({{ json_encode($step) }})" class="p-1.5 hover:text-white text-zinc-500"><i class="fas fa-pen text-[10px]"></i></button>
                                            <form action="{{ route('admin.omnichannel.steps.destroy', $step) }}" method="POST" data-confirm-delete>
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 hover:text-rose-400 text-zinc-500"><i class="fas fa-times text-[10px]"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="text-xs text-zinc-500 line-clamp-2 leading-relaxed mb-4">{{ $step->content }}</p>

                                    <!-- Step Options -->
                                    @if($step->type == 'menu')
                                        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-white/5">
                                            @foreach($step->options as $opt)
                                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/5 text-[9px] font-bold">
                                                    <span class="text-indigo-400">{{ $opt->trigger_value }}</span>
                                                    <span class="text-zinc-400">{{ $opt->label }}</span>
                                                    <form action="{{ route('admin.omnichannel.options.destroy', $opt) }}" method="POST" class="ml-1">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-zinc-600 hover:text-rose-400"><i class="fas fa-times"></i></button>
                                                    </form>
                                                </div>
                                            @endforeach
                                            <button @click="openCreateOption({{ $step->id }})" class="px-3 py-1.5 rounded-xl border border-dashed border-white/10 text-[9px] font-bold text-zinc-500 hover:text-white hover:border-white/20 transition-all">
                                                <i class="fas fa-plus mr-1"></i> Opção
                                            </button>
                                        </div>
                                    @elseif($step->next_step_id)
                                        <div class="mt-4 pt-4 border-t border-white/5 text-[9px] font-black uppercase tracking-widest text-zinc-600">
                                            Próximo Passo: <span class="text-indigo-400">#{{ $step->next_step_id }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- MODAL: BOT -->
    <div x-show="modalBot" class="fixed inset-0 z-[1000] flex items-center justify-center p-6" x-cloak>
        <div class="absolute inset-0 bg-black/80 backdrop-blur-xl" @click="modalBot = false"></div>
        <div class="relative w-full max-w-xl bg-zinc-950 rounded-[3rem] border border-white/10 shadow-2xl overflow-hidden animate-[nxPopIn_0.3s_ease-out]">
            <div class="p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-500"><i class="fas fa-cog text-xl"></i></div>
                    <h2 class="text-2xl font-black text-white" x-text="editMode ? 'Configurações do Agente' : 'Novo Agente Digital'"></h2>
                </div>
                
                <form :action="editMode ? `/admin/omnichannel/bots/${botData.id}` : '{{ route('admin.omnichannel.bots.store') }}'" method="POST" class="space-y-6">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Nome de Identificação</label>
                            <input type="text" name="name" x-model="botData.name" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">WhatsApp (DDI+DDD+Num)</label>
                            <input type="text" name="whatsapp_phone" x-model="botData.whatsapp_phone" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>
                        <div class="flex items-end pb-4">
                            <label class="relative flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-model="botData.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-zinc-400">Ativo</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Mensagem de Fora de Horário</label>
                        <textarea name="out_of_office_message" x-model="botData.out_of_office_message" rows="3" class="w-full bg-white/5 border border-white/10 rounded-3xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all"></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="modalBot = false" class="flex-1 px-8 py-4 rounded-2xl bg-white/5 text-zinc-400 font-bold hover:bg-white/10 transition-all">Cancelar</button>
                        <button type="submit" class="flex-1 px-8 py-4 rounded-2xl bg-indigo-600 text-white font-bold shadow-xl shadow-indigo-600/20 hover:brightness-110 active:scale-95 transition-all">Salvar Agente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: STEP -->
    <div x-show="modalStep" class="fixed inset-0 z-[1000] flex items-center justify-center p-6" x-cloak>
        <div class="absolute inset-0 bg-black/80 backdrop-blur-xl" @click="modalStep = false"></div>
        <div class="relative w-full max-w-xl bg-zinc-950 rounded-[3rem] border border-white/10 shadow-2xl overflow-hidden animate-[nxPopIn_0.3s_ease-out]">
            <div class="p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-500"><i class="fas fa-project-diagram text-xl"></i></div>
                    <h2 class="text-2xl font-black text-white" x-text="editMode ? 'Editar Passo do Roteiro' : 'Novo Passo de Atendimento'"></h2>
                </div>
                
                <form :action="editMode ? `/admin/omnichannel/steps/${stepData.id}` : `/admin/omnichannel/bots/${currentBotId}/steps`" method="POST" class="space-y-6">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Identificação do Passo</label>
                            <input type="text" name="label" x-model="stepData.label" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Tipo de Resposta</label>
                            <select name="type" x-model="stepData.type" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none appearance-none">
                                <option value="message">Mensagem Simples</option>
                                <option value="menu">Menu de Opções</option>
                                <option value="question">Pergunta Aberta</option>
                                <option value="transfer">Transferir Humano</option>
                            </select>
                        </div>
                        <div class="flex items-end pb-4">
                            <label class="relative flex items-center cursor-pointer">
                                <input type="checkbox" name="is_start" value="1" x-model="stepData.is_start" class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-zinc-400">Passo Inicial</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Conteúdo da Mensagem</label>
                        <textarea name="content" x-model="stepData.content" rows="4" required class="w-full bg-white/5 border border-white/10 rounded-3xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all"></textarea>
                    </div>

                    <div x-show="stepData.type !== 'menu'">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Encaminhar para Próximo Passo (#ID)</label>
                        <input type="number" name="next_step_id" x-model="stepData.next_step_id" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="modalStep = false" class="flex-1 px-8 py-4 rounded-2xl bg-white/5 text-zinc-400 font-bold hover:bg-white/10 transition-all">Cancelar</button>
                        <button type="submit" class="flex-1 px-8 py-4 rounded-2xl bg-indigo-600 text-white font-bold shadow-xl shadow-indigo-600/20 hover:brightness-110 active:scale-95 transition-all">Salvar Passo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: OPTION -->
    <div x-show="modalOption" class="fixed inset-0 z-[1000] flex items-center justify-center p-6" x-cloak>
        <div class="absolute inset-0 bg-black/80 backdrop-blur-xl" @click="modalOption = false"></div>
        <div class="relative w-full max-w-md bg-zinc-950 rounded-[3rem] border border-white/10 shadow-2xl overflow-hidden animate-[nxPopIn_0.3s_ease-out]">
            <div class="p-10 text-center">
                <div class="w-16 h-16 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-500 mx-auto mb-6"><i class="fas fa-list-ul text-2xl"></i></div>
                <h2 class="text-2xl font-black text-white mb-2">Nova Opção do Menu</h2>
                <p class="text-zinc-500 text-sm mb-8">Defina o gatilho e o destino.</p>
                
                <form :action="`/admin/omnichannel/steps/${currentStepId}/options`" method="POST" class="space-y-6">
                    @csrf
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Gatilho (ex: 1, Sim, Preço)</label>
                        <input type="text" name="trigger_value" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Rótulo Visual</label>
                        <input type="text" name="label" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Passo de Destino (#ID)</label>
                        <input type="number" name="destination_step_id" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>

                    <button type="submit" class="w-full px-8 py-4 rounded-2xl bg-indigo-600 text-white font-bold shadow-xl shadow-indigo-600/20 hover:brightness-110 active:scale-95 transition-all">Adicionar Opção</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes nxPopIn { 0% { opacity: 0; transform: scale(0.9) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
    [x-cloak] { display: none !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
