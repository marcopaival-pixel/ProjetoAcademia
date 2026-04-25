@extends('layouts.admin')

@section('title', 'Detalhes do Lead')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.leads.index') }}" class="w-10 h-10 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">{{ $lead->nome }}</h2>
                <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">{{ $lead->empresa ?? 'Empresa não informada' }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.leads.edit', $lead) }}" class="px-6 py-3 bg-zinc-900 border border-white/5 rounded-2xl text-[10px] text-zinc-400 font-black uppercase tracking-widest hover:bg-zinc-800 transition-all flex items-center gap-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form action="{{ route('admin.leads.delete', $lead) }}" method="POST"
                data-confirm-delete
                data-confirm-title="Excluir lead"
                data-confirm-message="Tem certeza de que deseja excluir este lead? Esta ação não pode ser desfeita.">
                @csrf @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600/10 border border-red-500/20 rounded-2xl text-[10px] text-red-500 font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Col -->
        <div class="space-y-8">
            <!-- Profile Card -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-24 h-24 rounded-[2rem] bg-zinc-950 flex items-center justify-center border border-white/10 text-4xl text-blue-500 mb-4">
                        {{ substr($lead->nome, 0, 1) }}
                    </div>
                    @php
                        $statusColors = [
                            'Novo' => 'blue',
                            'Em contato' => 'amber',
                            'Em negociação' => 'purple',
                            'Convertido' => 'emerald',
                            'Perdido' => 'red',
                        ];
                        $color = $statusColors[$lead->status] ?? 'zinc';
                    @endphp
                    <span class="px-4 py-1.5 bg-{{ $color }}-500/10 border border-{{ $color }}-500/20 text-{{ $color }}-500 text-[10px] font-black uppercase rounded-xl">
                        {{ $lead->status }}
                    </span>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg bg-zinc-950 flex items-center justify-center text-zinc-500 text-xs shadow-inner">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">Email</p>
                            <p class="text-sm font-bold text-zinc-300 truncate">{{ $lead->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg bg-zinc-950 flex items-center justify-center text-zinc-500 text-xs shadow-inner">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">Telefone</p>
                            <p class="text-sm font-bold text-zinc-300 truncate">{{ $lead->telefone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg bg-zinc-950 flex items-center justify-center text-zinc-500 text-xs shadow-inner">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">Origem</p>
                            <p class="text-sm font-bold text-zinc-300 truncate">{{ $lead->origem ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-white/5">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($lead->responsavel?->name ?? 'A') }}&background=18181b&color=a1a1aa" class="w-8 h-8 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">Responsável</p>
                            <p class="text-sm font-bold text-zinc-300 truncate">{{ $lead->responsavel?->name ?? 'Não atribuído' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demo Access Card -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-6">Acesso Demonstração</h3>
                
                @if($lead->converted_user_id)
                    @php($demoUser = \App\Models\User::find($lead->converted_user_id))
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl mb-4">
                        <p class="text-[10px] text-emerald-500 font-black uppercase tracking-widest">Acesso Ativo</p>
                        <p class="text-[9px] text-zinc-500 font-bold mt-1 tracking-tight">Expira em: {{ $demoUser->demo_expires_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                    </div>
                @endif

                <form action="{{ route('admin.leads.generate-demo', $lead) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-zinc-950 border border-white/5 rounded-2xl text-[9px] text-center text-zinc-400 font-black uppercase tracking-widest hover:border-blue-500/50 hover:text-white transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-magic"></i> {{ $lead->converted_user_id ? 'Renovar Demo' : 'Gerar Acesso Demo' }}
                    </button>
                </form>
                <p class="text-[8px] text-zinc-700 font-bold uppercase text-center mt-3 tracking-widest leading-relaxed">Cria uma conta temporária de 7 dias para o e-mail do lead.</p>
            </div>

            <!-- Financial Card -->
            <div class="bg-gradient-to-br from-emerald-500/[0.05] to-emerald-900/[0.02] border border-emerald-500/10 rounded-[2.5rem] p-8">
                <h3 class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="fas fa-dollar-sign"></i> Potencial de Negócio
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[9px] text-emerald-500/40 font-black uppercase tracking-widest">Valor Estimado</p>
                        <p class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($lead->valor_estimado, 2, ',', '.') }}</p>
                    </div>
                    @if($lead->previsao_fechamento)
                    <div>
                        <p class="text-[9px] text-emerald-500/40 font-black uppercase tracking-widest">Expectativa de Fechamento</p>
                        <p class="text-sm font-black text-emerald-400 uppercase tracking-widest mt-1">
                            <i class="far fa-calendar-alt mr-1"></i> {{ $lead->previsao_fechamento->format('d/m/Y') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content Col (Timeline) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- New Interaction Form -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <h3 class="text-sm font-black text-white tracking-tight mb-6">Registrar Interação</h3>
                <form action="{{ route('admin.leads.interaction.store', $lead) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tipo de Contato</label>
                            <select name="tipo_contato" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-4 py-3 text-white text-sm outline-none focus:border-blue-500/50">
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Ligação">Ligação</option>
                                <option value="Email">Email</option>
                                <option value="Reunião">Reunião</option>
                                <option value="Demo">Demonstração</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] text-zinc-500 font-black uppercase tracking-widest ml-1">Data</label>
                            <input type="datetime-local" name="data_contato" value="{{ date('Y-m-d\TH:i') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-4 py-3 text-white text-sm outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] text-zinc-500 font-black uppercase tracking-widest ml-1">Resumo da Conversa</label>
                        <textarea name="descricao" rows="3" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-4 py-3 text-white text-sm outline-none focus:border-blue-500/50 resize-none" placeholder="O que foi conversado? Quais os próximos passos?"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-blue-600 rounded-xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                            Salvar Histórico
                        </button>
                    </div>
                </form>
            </div>

            <!-- Proposals Section -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-white tracking-tight">Propostas Geradas</h3>
                    <a href="{{ route('admin.proposals.create', ['lead_id' => $lead->id]) }}" class="text-[9px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-400 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Nova Proposta
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($lead->proposals as $proposal)
                    <div class="flex items-center justify-between bg-zinc-950 border border-white/5 p-4 rounded-2xl hover:bg-zinc-900 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-blue-500 border border-white/5 shadow-inner">
                                <i class="fas fa-file-invoice-dollar text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ $proposal->plan->name }}</p>
                                <p class="text-xs font-black text-white">R$ {{ number_format($proposal->valor - $proposal->desconto, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            @php
                                $colors = ['Pendente' => 'zinc', 'Enviada' => 'blue', 'Aprovada' => 'emerald', 'Rejeitada' => 'red'];
                                $color = $colors[$proposal->status] ?? 'zinc';
                            @endphp
                            <span class="px-2 py-0.5 bg-{{$color}}-500/10 border border-{{$color}}-500/20 text-{{$color}}-500 text-[7px] font-black uppercase rounded">
                                {{ $proposal->status }}
                            </span>
                            <a href="{{ route('admin.proposals.show', $proposal) }}" class="p-2 bg-zinc-800 rounded-lg text-zinc-500 hover:text-white transition-colors opacity-0 group-hover:opacity-100">
                                <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 bg-zinc-950/30 border border-dashed border-white/5 rounded-2xl">
                        <p class="text-zinc-600 text-[10px] uppercase font-black tracking-widest italic">Nenhuma proposta para este lead</p>
                    </div>
                    @endforelse
                </div>
            </div>

            </div>

            <!-- Onboarding Checklist -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-sm font-black text-white tracking-tight">Onboarding Técnico</h3>
                    <div class="text-[9px] font-black text-emerald-500 uppercase tracking-widest bg-emerald-500/10 px-2 py-1 rounded">
                        {{ $lead->onboardingSteps->where('is_completed', true)->count() }} / {{ $lead->onboardingSteps->count() ?: 0 }} concluídos
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($lead->onboardingSteps as $step)
                    <div class="flex items-center gap-4 group">
                        <form action="{{ route('admin.leads.onboarding.toggle', [$lead, $step]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-6 h-6 rounded-lg border-2 {{ $step->is_completed ? 'bg-blue-600 border-blue-600 text-white' : 'border-white/10 hover:border-blue-500/50' }} flex items-center justify-center transition-all">
                                @if($step->is_completed) <i class="fas fa-check text-[10px]"></i> @endif
                            </button>
                        </form>
                        <div class="flex-1">
                            <p class="text-xs font-bold {{ $step->is_completed ? 'text-zinc-500 line-through' : 'text-zinc-300' }}">{{ $step->title }}</p>
                            @if($step->is_completed && $step->completed_at)
                                <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-widest mt-0.5">Concluído {{ $step->completed_at->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 bg-zinc-950/30 border border-dashed border-white/5 rounded-2xl">
                         <p class="text-zinc-700 text-[9px] font-black uppercase tracking-widest">Nenhum passo definido.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Contracts Card -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <h3 class="text-sm font-black text-white tracking-tight mb-6">Contratos</h3>
                <div class="space-y-4">
                    @forelse($lead->contracts as $contract)
                    <div class="flex items-center justify-between bg-zinc-950/50 p-4 rounded-2xl border border-white/5">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-contract text-blue-500"></i>
                            <span class="text-xs font-bold text-zinc-300">Contrato #{{ $contract->id }}</span>
                        </div>
                        <span class="text-[8px] font-black uppercase px-2 py-1 bg-{{ $contract->status == 'Assinado' ? 'emerald' : 'zinc' }}-500/10 text-{{ $contract->status == 'Assinado' ? 'emerald' : 'zinc' }}-500 rounded">{{ $contract->status }}</span>
                    </div>
                    @empty
                    <p class="text-center text-[10px] text-zinc-600 italic">Aguardando assinatura da proposta.</p>
                    @endforelse
                </div>
            </div>

            <!-- Timeline -->
            <div class="space-y-6">
                <h3 class="text-sm font-black text-zinc-500 uppercase tracking-widest flex items-center gap-4">
                    Histórico de Relacionamento
                    <span class="flex-1 h-px bg-white/5"></span>
                </h3>

                <div class="space-y-6 relative ml-4">
                    <div class="absolute left-[-16px] top-4 bottom-4 w-px bg-white/5"></div>
                    
                    @forelse($lead->interactions as $interaction)
                    <div class="relative pl-8 group">
                        <div class="absolute left-[-24px] top-1 w-4 h-4 rounded-full bg-zinc-900 border-2 border-white/10 group-hover:border-blue-500/50 transition-colors flex items-center justify-center">
                            <div class="w-1 h-1 rounded-full bg-blue-500"></div>
                        </div>
                        
                        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] hover:bg-zinc-900/60 transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-500 text-[8px] font-black uppercase rounded">
                                        {{ $interaction->tipo_contato }}
                                    </span>
                                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ $interaction->user->name }} &bull; {{ $interaction->data_contato->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="text-[9px] text-zinc-700 font-black uppercase">{{ $interaction->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-zinc-300 leading-relaxed">{{ $interaction->descricao }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-20 bg-zinc-900/20 border border-dashed border-white/5 rounded-[2.5rem]">
                        <p class="text-zinc-600 italic text-sm">Nenhuma interação registrada ainda.</p>
                    </div>
                    @endforelse
                    
                    <!-- Lead Creation Event -->
                    <div class="relative pl-8 group opacity-40">
                         <div class="absolute left-[-24px] top-1 w-4 h-4 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center">
                            <i class="fas fa-star text-[6px] text-zinc-600"></i>
                        </div>
                        <div class="text-xs text-zinc-600 font-black uppercase tracking-widest">Lead capturado no sistema &bull; {{ $lead->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
