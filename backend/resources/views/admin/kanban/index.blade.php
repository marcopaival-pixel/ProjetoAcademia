@extends('layouts.admin')

@section('title', 'Gestão de Tarefas')

@section('content')
<div class="space-y-8 animate-fade-in flex flex-col h-[calc(100vh-12rem)]">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight italic uppercase">Gestão de Operações</h2>
            <p class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1 italic">NexShape Kanban System</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openCreateModal()" class="px-8 py-3 bg-emerald-600 rounded-2xl text-[10px] text-zinc-950 font-black uppercase tracking-widest hover:bg-emerald-500 transition-all flex items-center gap-3 shadow-2xl shadow-emerald-600/20 group">
                <i data-lucide="plus" class="w-4 h-4 group-hover:rotate-90 transition-transform"></i>
                Nova Tarefa
            </button>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 overflow-x-auto pb-6 custom-scrollbar">
        <div class="flex gap-6 h-full min-w-max px-2">
            @foreach($statuses as $status)
            <div class="w-80 flex flex-col bg-[#0b0e14]/50 backdrop-blur-md border border-white/5 rounded-[2.5rem] p-4 group/lane shadow-2xl">
                <div class="flex items-center justify-between mb-6 px-4">
                    <div class="flex items-center gap-3">
                        @php
                            $dotColor = match($status) {
                                'Pendente' => 'zinc',
                                'Em andamento' => 'blue',
                                'Concluído' => 'emerald',
                                'Cancelado' => 'rose',
                                default => 'zinc'
                            };
                        @endphp
                        <span class="w-2 h-2 rounded-full bg-{{ $dotColor }}-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">{{ $status }}</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg bg-zinc-950 text-[9px] text-zinc-600 font-black border border-white/5">{{ count($tasksByStatus[$status] ?? []) }}</span>
                </div>

                <div class="flex-1 space-y-4 overflow-y-auto pr-1 kanban-lane min-h-[100px]" data-status="{{ $status }}">
                    @foreach($tasksByStatus[$status] ?? [] as $task)
                    <div class="bg-zinc-900/80 backdrop-blur-sm border border-white/5 p-5 rounded-[2rem] hover:border-emerald-500/30 hover:bg-zinc-800/40 transition-all cursor-grab active:cursor-grabbing shadow-xl group/card relative" 
                         data-id="{{ $task->id }}"
                         onclick="openEditModal({{ json_encode($task) }})">
                        
                        <div class="flex justify-between items-start mb-4">
                            @php
                                $priorityColor = match($task->priority) {
                                    'Baixa' => 'zinc',
                                    'Média' => 'blue',
                                    'Alta' => 'amber',
                                    'Crítica' => 'rose',
                                    default => 'zinc'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg bg-{{ $priorityColor }}-500/10 border border-{{ $priorityColor }}-500/20 text-{{ $priorityColor }}-500 text-[8px] font-black uppercase tracking-widest">
                                {{ $task->priority }}
                            </span>
                            
                            @if($task->assignedTo)
                            <div class="w-7 h-7 rounded-lg overflow-hidden border border-white/10 shadow-lg" title="{{ $task->assignedTo->name }}">
                                <img src="{{ $task->assignedTo->profile_photo_url }}" class="w-full h-full object-cover">
                            </div>
                            @endif
                        </div>

                        <h4 class="text-sm font-black text-white mb-2 tracking-tight group-hover/card:text-emerald-400 transition-colors">{{ $task->title }}</h4>
                        <p class="text-[10px] text-zinc-500 font-medium leading-relaxed line-clamp-2 italic">{{ $task->description }}</p>

                        <div class="mt-5 pt-4 border-t border-white/5 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-3 h-3 text-zinc-600"></i>
                                <span class="text-[9px] text-zinc-600 font-black uppercase">{{ $task->due_date ? $task->due_date->format('d/m') : 'S/ Data' }}</span>
                            </div>
                            <i data-lucide="more-horizontal" class="w-3.5 h-3.5 text-zinc-700 opacity-0 group-hover/card:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                    @endforeach
                    
                    @if(!isset($tasksByStatus[$status]) || count($tasksByStatus[$status]) == 0)
                    <div class="h-32 border-2 border-dashed border-white/5 rounded-[2rem] flex flex-col items-center justify-center opacity-30">
                        <i data-lucide="inbox" class="w-6 h-6 text-zinc-700 mb-2"></i>
                        <span class="text-[9px] text-zinc-700 font-black uppercase tracking-[0.2em]">Sem Tarefas</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div id="taskModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-zinc-950/90 backdrop-blur-xl" onclick="closeModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-[#0b0e14] border border-white/10 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-scale-in">
            <form id="taskForm" method="POST" action="{{ route('admin.kanban.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="p-10">
                    <div class="flex justify-between items-center mb-10">
                        <h3 id="modalTitle" class="text-2xl font-black text-white uppercase italic tracking-tighter">Nova Tarefa</h3>
                        <button type="button" onclick="closeModal()" class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-2 block">Título da Tarefa</label>
                            <input type="text" name="title" id="taskTitle" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-emerald-500/50 transition-all outline-none" placeholder="Ex: Ajustar gateway de pagamento">
                        </div>

                        <div>
                            <label class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-2 block">Descrição / Detalhes</label>
                            <textarea name="description" id="taskDescription" rows="4" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-emerald-500/50 transition-all outline-none" placeholder="O que precisa ser feito?"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-2 block">Status Inicial</label>
                                <select name="status" id="taskStatus" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-emerald-500/50 transition-all outline-none appearance-none">
                                    @foreach($statuses as $st)
                                        <option value="{{ $st }}">{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-2 block">Prioridade</label>
                                <select name="priority" id="taskPriority" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-emerald-500/50 transition-all outline-none appearance-none">
                                    @foreach($priorities as $pr)
                                        <option value="{{ $pr }}" {{ $pr == 'Média' ? 'selected' : '' }}>{{ $pr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-2 block">Responsável</label>
                                <select name="assigned_to" id="taskAssignedTo" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-emerald-500/50 transition-all outline-none appearance-none">
                                    <option value="">Não atribuído</option>
                                    @foreach($admins as $adm)
                                        <option value="{{ $adm->id }}">{{ $adm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-2 block">Prazo (Opcional)</label>
                                <input type="date" name="due_date" id="taskDueDate" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-emerald-500/50 transition-all outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex gap-4">
                        <button type="button" id="deleteBtn" class="hidden px-8 py-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all flex-1" onclick="deleteTask()">
                            Deletar
                        </button>
                        <button type="submit" class="px-8 py-4 bg-emerald-600 text-zinc-950 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 transition-all flex-1 shadow-2xl shadow-emerald-600/20">
                            Salvar Tarefa
                        </button>
                    </div>
                </div>
            </form>
            <form id="deleteForm" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lanes = document.querySelectorAll('.kanban-lane');
        
        lanes.forEach(lane => {
            new Sortable(lane, {
                group: 'kanban',
                animation: 250,
                ghostClass: 'opacity-20',
                chosenClass: 'scale-[1.02]',
                dragClass: 'shadow-2xl',
                onEnd: function (evt) {
                    const taskId = evt.item.dataset.id;
                    const newStatus = evt.to.dataset.status;
                    const newPosition = evt.newIndex;
                    
                    updateTaskStatus(taskId, newStatus, newPosition);
                }
            });
        });

        function updateTaskStatus(id, status, position) {
            fetch(`/admin/kanban/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    status: status,
                    position: position
                })
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    alert('Erro ao sincronizar tarefa');
                    window.location.reload();
                }
            });
        }
    });

    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Nova Tarefa';
        document.getElementById('taskForm').action = "{{ route('admin.kanban.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('deleteBtn').classList.add('hidden');
        
        // Reset form
        document.getElementById('taskTitle').value = '';
        document.getElementById('taskDescription').value = '';
        document.getElementById('taskStatus').value = 'Pendente';
        document.getElementById('taskPriority').value = 'Média';
        document.getElementById('taskAssignedTo').value = '';
        document.getElementById('taskDueDate').value = '';
        
        document.getElementById('taskModal').classList.remove('hidden');
    }

    function openEditModal(task) {
        event.stopPropagation();
        document.getElementById('modalTitle').innerText = 'Editar Tarefa';
        document.getElementById('taskForm').action = `/admin/kanban/${task.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('deleteBtn').classList.remove('hidden');
        
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskDescription').value = task.description || '';
        document.getElementById('taskStatus').value = task.status;
        document.getElementById('taskPriority').value = task.priority;
        document.getElementById('taskAssignedTo').value = task.assigned_to || '';
        document.getElementById('taskDueDate').value = task.due_date ? task.due_date.split('T')[0] : '';
        
        document.getElementById('taskModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('taskModal').classList.add('hidden');
    }

    function deleteTask() {
        if(confirm('Deseja realmente remover esta tarefa?')) {
            const action = document.getElementById('taskForm').action;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = action;
            deleteForm.submit();
        }
    }
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    
    .animate-scale-in { animation: scaleIn 0.3s ease-out forwards; }
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>
@endpush
@endsection
