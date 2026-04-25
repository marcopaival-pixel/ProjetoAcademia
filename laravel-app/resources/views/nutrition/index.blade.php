@extends('layouts.app')

@section('title', 'Central de Nutrição — NexShape')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('nutritionHub', () => ({
            goalModalOpen: false, 
            supplementModalOpen: false,
            stackModalOpen: false,
            editStackModalOpen: false,
            aiStackModalOpen: false,
            auditModalOpen: false,
            confirmDeleteModalOpen: false,
            deleteActionUrl: '',
            deleteItemName: '',
            confirmDelete(url, name) {
                this.deleteActionUrl = url;
                this.deleteItemName = name;
                this.confirmDeleteModalOpen = true;
            },
            aiCredits: {{ (int)(auth()->user()->ai_credits ?? 0) }},
            aiStackLoading: false,
            aiStackSuggestion: null,
            selectedStackId: null,
            selectedStack: null,
            suggestionModalOpen: false,
            mealSuggestion: '',
            loadingMeal: false,
            auditResult: '',
            loadingAudit: false,
            supplementSearch: '',
            catalogResults: [],
            showCatalog: false,
            async searchSupplement() {
                if (this.supplementSearch.length < 2) {
                    this.catalogResults = [];
                    this.showCatalog = false;
                    return;
                }
                try {
                    const resp = await fetch(`/smart-stacks/search-catalog?q=${encodeURIComponent(this.supplementSearch)}`);
                    this.catalogResults = await resp.json();
                    this.showCatalog = this.catalogResults.length > 0;
                } catch (e) {
                    console.error(e);
                }
            },
            selectFromCatalog(item) {
                this.supplementSearch = item.name;
                const dosageInput = document.querySelector('input[name="dosage"]');
                const unitSelect = document.querySelector('select[name="unit"]');
                if (dosageInput && item.default_dosage) dosageInput.value = item.default_dosage;
                if (unitSelect && item.default_unit) unitSelect.value = item.default_unit;
                this.showCatalog = false;
            },
            async takeSupplement(id) {
                try {
                    const resp = await fetch(`/supplements/${id}/take`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const data = await resp.json();
                    if (data.success) {
                        // Success feedback
                    }
                } catch (e) {}
            },
            async getAISuggestion() {
                this.aiStackLoading = true;
                this.aiStackModalOpen = true;
                this.aiStackSuggestion = null;
                try {
                    const resp = await fetch('{{ route('smart-stacks.suggest') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const data = await resp.json();
                    if (data.success) {
                        this.aiStackSuggestion = data.suggestion;
                        this.aiCredits--;
                    } else {
                        alert(data.error || 'Erro ao gerar sugestão.');
                    }
                } catch (e) {
                    alert('Erro ao gerar sugestão da IA.');
                } finally {
                    this.aiStackLoading = false;
                }
            },
            async adoptAISuggestion() {
                if (!this.aiStackSuggestion) return;
                try {
                    const resp = await fetch('{{ route('smart-stacks.adopt-suggestion') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ suggestion: this.aiStackSuggestion })
                    });
                    const data = await resp.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Erro ao adotar o stack.');
                    }
                } catch (e) {
                    alert('Erro na comunicação com o servidor.');
                }
            },
            async generateMeal() {
                this.loadingMeal = true;
                this.suggestionModalOpen = true;
                this.mealSuggestion = 'Analisando seus macros e gerando a melhor opção...';
                try {
                    const resp = await fetch('{{ route('nutrition.suggest-meal') }}');
                    const data = await resp.json();
                    if (data.success) {
                        this.mealSuggestion = data.suggestion;
                        this.aiCredits--;
                    } else {
                        this.mealSuggestion = 'Erro ao gerar sugestão: ' + data.error;
                    }
                } catch (e) {
                    this.mealSuggestion = 'Erro na comunicação com o servidor.';
                } finally {
                    this.loadingMeal = false;
                }
            },
            async runAudit() {
                this.loadingAudit = true;
                this.auditModalOpen = true;
                this.auditResult = 'Analisando sua semana...';
                try {
                    const resp = await fetch('{{ route('nutrition.audit') }}');
                    const data = await resp.json();
                    if (data.success) {
                        this.auditResult = data.audit;
                        this.aiCredits--;
                    } else {
                        this.auditResult = 'Erro na auditoria: ' + data.error;
                    }
                } catch (e) {
                    this.auditResult = 'Erro na comunicação com o servidor.';
                } finally {
                    this.loadingAudit = false;
                }
            },
            async addWaterInHub(amount) {
                try {
                    const resp = await fetch('{{ route('nutrition.add-water') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ amount })
                    });
                    const data = await resp.json();
                    if (resp.ok && data.success) {
                        window.location.reload(); 
                    } else {
                        alert('Erro ao registrar água: ' + (data.message || 'Erro desconhecido'));
                    }
                } catch (e) {
                    console.error('Erro ao registrar água', e);
                    alert('Falha de conexão ao registrar água.');
                }
            },
            async adoptSuggestedMeal() {
                if (!this.mealSuggestion) return;
                
                let metrics = { cal: 0, p: 0, c: 0, f: 0 };
                const regex = /METRICS:\s*\[cal:(\d+),\s*p:(\d+),\s*c:(\d+),\s*f:(\d+)\]/i;
                const match = this.mealSuggestion.match(regex);
                
                if (match) {
                    metrics = {
                        cal: parseInt(match[1]),
                        p: parseInt(match[2]),
                        c: parseInt(match[3]),
                        f: parseInt(match[4])
                    };
                } else {
                    metrics = {
                        cal: {{ (int)($remaining->cal ?? 0) }},
                        p: {{ (int)($remaining->p ?? 0) }},
                        c: {{ (int)($remaining->c ?? 0) }},
                        f: {{ (int)($remaining->f ?? 0) }}
                    };
                }

                try {
                    const resp = await fetch('{{ route('nutrition.adopt-meal') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ metrics, text: this.mealSuggestion })
                    });
                    if (resp.ok) {
                        window.location.href = '{{ route('diary') }}?flash=added';
                    }
                } catch (e) {
                    alert('Erro ao registrar refeição.');
                }
            },
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            selectedFood: null,
            amount: 100,
            unit: 'g',
            unitLabels: {
                'g': 'Gramas', 'ml': 'Mililitros', 'tbsp': 'Colher de sopa',
                'tsp': 'Colher de chá', 'cup': 'Xícara', 'slice': 'Fatia', 'un': 'Unidade'
            },
            conversionFactors: {
                'g': 1, 'ml': 1, 'tbsp': 15, 'tsp': 5, 'cup': 240, 'slice': 30, 'un': 100
            },
            favorites: [],
            loadingFavorites: false,
            async searchFood() {
                if (this.searchQuery.length < 3) {
                    this.searchResults = [];
                    return;
                }
                this.isSearching = true;
                try {
                    const resp = await fetch(`/api/food/search?q=${encodeURIComponent(this.searchQuery)}`);
                    const data = await resp.json();
                    if (data.ok) {
                        this.searchResults = data.products;
                    }
                } catch (e) {} finally { this.isSearching = false; }
            },
            async selectProduct(code) {
                try {
                    const resp = await fetch(`/api/food/product/${code}`);
                    const data = await resp.json();
                    if (data.ok) {
                        this.selectedFood = data.product;
                        this.searchQuery = data.product.name;
                        this.searchResults = [];
                    }
                } catch (e) {}
            },
            async loadFavorites(mealType = '') {
                this.loadingFavorites = true;
                try {
                    const resp = await fetch(`{{ route('nutrition.api.favorites') }}?meal_type=${mealType}`);
                    this.favorites = await resp.json();
                } catch (e) {} finally { this.loadingFavorites = false; }
            },
            aiInput: '',
            isProcessingAI: false,
            async processAI() {
                if (!this.aiInput) return;
                this.isProcessingAI = true;
                try {
                    const resp = await fetch('{{ route('nutrition.api.natural-language') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ text: this.aiInput })
                    });
                    const data = await resp.json();
                    if (data.success) {
                        const food = data.foods[0];
                        this.selectedFood = {
                            name: food.name,
                            energy_kcal: food.kcal,
                            protein_g: food.p,
                            carbohydrates_g: food.c,
                            fat_g: food.f
                        };
                        this.amount = food.amount.replace(/[^0-9]/g, '') || 100;
                        this.aiInput = '';
                    }
                } catch (e) {} finally { this.isProcessingAI = false; }
            },
            get currentMacros() {
                if (!this.selectedFood) return { kcal: 0, p: 0, c: 0, f: 0 };
                const factor = this.conversionFactors[this.unit] || 1;
                const multiplier = (this.amount * factor) / 100;
                return {
                    kcal: Math.round(this.selectedFood.energy_kcal * multiplier),
                    p: (this.selectedFood.protein_g * multiplier).toFixed(1),
                    c: (this.selectedFood.carbohydrates_g * multiplier).toFixed(1),
                    f: (this.selectedFood.fat_g * multiplier).toFixed(1)
                };
            },
            repeatModalOpen: false,
            repeatMealType: '',
            repeatSource: 'yesterday',
            repeatTargetDate: '{{ $date }}',
            openRepeatModal(mtype) {
                this.repeatMealType = mtype;
                this.repeatModalOpen = true;
            }
        }));
    });
</script>

<div x-data="nutritionHub" class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    <!-- Futuristic Tab Navigation -->
    <!-- Futuristic Tab Navigation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/5 pb-1">
        <div class="flex items-center gap-4">
            <a href="{{ route('nutrition.index', ['tab' => 'dashboard', 'date' => $date]) }}" 
               class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative {{ $tab === 'dashboard' ? 'text-blue-500' : 'text-zinc-500 hover:text-zinc-300' }}">
               Dashboard
               @if($tab === 'dashboard')
                   <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 rounded-t-full shadow-[0_-4px_12px_rgba(59,130,246,0.5)]"></div>
               @endif
            </a>
            <a href="{{ route('nutrition.index', ['tab' => 'diary', 'date' => $date]) }}" 
               class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative {{ $tab === 'diary' ? 'text-blue-500' : 'text-zinc-500 hover:text-zinc-300' }}">
               Diário Alimentar
               @if($tab === 'diary')
                   <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 rounded-t-full shadow-[0_-4px_12px_rgba(59,130,246,0.5)]"></div>
               @endif
            </a>
            <a href="{{ route('nutrition.index', ['tab' => 'stacks', 'date' => $date]) }}" 
               class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative {{ $tab === 'stacks' ? 'text-blue-500' : 'text-zinc-500 hover:text-zinc-300' }}">
               Smart Stack
               @if($tab === 'stacks')
                   <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 rounded-t-full shadow-[0_-4px_12px_rgba(59,130,246,0.5)]"></div>
               @endif
            </a>
        </div>

        <div class="flex items-center bg-zinc-900/50 backdrop-blur-xl p-1.5 rounded-2xl border border-white/5 shadow-2xl mb-2 md:mb-0">
            <a href="{{ route('nutrition.index', ['tab' => $tab, 'date' => date('Y-m-d', strtotime($date . ' -1 day'))]) }}" class="w-10 h-10 flex items-center justify-center text-zinc-400 hover:bg-white/5 hover:text-white rounded-xl transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            
            <label class="relative cursor-pointer px-6 text-center min-w-[160px] group/cal-global">
                <input type="date" value="{{ $date }}" 
                       onchange="window.location.href = '{{ route('nutrition.index', ['tab' => $tab]) }}&date=' + this.value"
                       class="absolute inset-0 opacity-0 cursor-pointer z-10">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-calendar-alt text-[10px] text-blue-500 group-hover/cal-global:scale-110 transition-transform"></i>
                    <p class="text-white font-black text-xs uppercase tracking-widest">
                        {{ date('d/m/Y', strtotime($date)) }}
                    </p>
                </div>
            </label>

            <a href="{{ route('nutrition.index', ['tab' => $tab, 'date' => date('Y-m-d', strtotime($date . ' + 1 day'))]) }}" class="w-10 h-10 flex items-center justify-center text-zinc-400 hover:bg-white/5 hover:text-white rounded-xl transition-all">
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
        </div>
    </div>

    @if($tab === 'dashboard')
        <!-- Barra de Metas Nutricionais -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $targetP = $macroTargets['p'] ?? 0;
            $targetC = $macroTargets['c'] ?? 0;
            $targetF = $macroTargets['f'] ?? 0;
            
            $percCal = (int)min((($selectedDateSums->cal ?? 0) / ($targetKcal ?: 1)) * 100, 100);
            $percP = (int)min((($selectedDateSums->p ?? 0) / ($targetP ?: 1)) * 100, 100);
            $percC = (int)min((($selectedDateSums->c ?? 0) / ($targetC ?: 1)) * 100, 100);
            $percF = (int)min((($selectedDateSums->f ?? 0) / ($targetF ?: 1)) * 100, 100);
        @endphp
        
        <div class="bg-zinc-900/40 border border-white/5 p-5 rounded-3xl relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Calorias</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->cal ?? 0, 0) }} / {{ number_format($targetKcal, 0) }} kcal</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: {{ $percCal }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 p-5 rounded-3xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest text-rose-400">Proteínas</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->p ?? 0, 0) }} / {{ number_format($targetP, 0) }}g</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-rose-500 transition-all duration-1000" style="width: {{ $percP }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 p-5 rounded-3xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest text-blue-400">Carboidratos</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->c ?? 0, 0) }} / {{ number_format($targetC, 0) }}g</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-blue-400 transition-all duration-1000" style="width: {{ $percC }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 p-5 rounded-3xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest text-amber-400">Gorduras</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->f ?? 0, 0) }} / {{ number_format($targetF, 0) }}g</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 transition-all duration-1000" style="width: {{ $percF }}%"></div>
            </div>
        </div>
    </div>

    <!-- Header Clean -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-5">
            <a href="{{ route('patient.reports.index') }}" class="w-14 h-14 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-500 border border-white/5 hover:text-white transition-all" title="Voltar ao Hub">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-white">Central de Nutrição</h1>
                <p class="text-zinc-500 text-sm mt-1">Gestão inteligente de metas e balanço calórico.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click="runAudit()" class="px-5 py-2.5 bg-purple-600/10 text-purple-400 font-bold text-xs rounded-xl border border-purple-500/20 hover:bg-purple-600 hover:text-white transition-all flex items-center gap-2">
                <i class="fas fa-microscope text-[10px]"></i>
                Auditoria IA
            </button>
            <button @click="goalModalOpen = true" class="px-5 py-2.5 bg-blue-600 text-white font-black text-xs rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/10">
                Ajustar Estratégia
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-xs font-bold animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Column 1: Metabolic & Consistency -->
        <div class="space-y-8">
            <!-- Metabolic Analysis -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Análise Metabólica
                </h3>

                @if($stats['ok'])
                <div class="space-y-6">
                    <div>
                        <span class="text-[10px] text-zinc-600 font-bold uppercase block mb-1">Taxa Basal (TMB)</span>
                        <p class="text-xl font-black text-white">{{ number_format($stats['bmr'], 0, ',', '.') }} <small class="text-zinc-500 text-[10px]">kcal</small></p>
                    </div>
                    <div>
                        <span class="text-[10px] text-zinc-600 font-bold uppercase block mb-1">Gasto Total (TDEE)</span>
                        <p class="text-xl font-black text-amber-500">{{ number_format($stats['tdee'], 0, ',', '.') }} <small class="text-zinc-600 text-[10px]">kcal</small></p>
                    </div>
                    <div class="pt-6 border-t border-white/5">
                        <span class="text-[10px] text-blue-400 font-black uppercase block mb-1">Meta Diária</span>
                        <p class="text-3xl font-black text-white tracking-tight">{{ number_format($targetKcal, 0, ',', '.') }} <small class="text-zinc-500 text-xs italic font-normal">kcal</small></p>
                    </div>
                </div>
                @else
                <div class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5 text-zinc-500 text-[10px] italic">
                    {{ $stats['message'] }}
                </div>
                @endif
            </div>

            <!-- Consistency Card -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Consistência Semanal
                </h3>
                <div class="text-center py-2">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-4">
                        <span class="text-2xl font-black text-emerald-400">{{ $consistencyCount }}<small class="text-xs">/7</small></span>
                    </div>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase">Dias no alvo</p>
                    <p class="text-xs text-zinc-400 mt-2">
                        @if($consistencyCount >= 6) Excelente disciplina!
                        @elseif($consistencyCount >= 4) Boa constância.
                        @else Foco no objetivo! @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Column 2 & 3: Main Charts -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Calorie Trend Chart -->
            <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Tendência de Calorias (15 dias)
                    </h3>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-500">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Real
                        </span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-500">
                            <span class="w-2 h-2 rounded-full border border-dashed border-zinc-500"></span> Meta
                        </span>
                    </div>
                </div>
                <div style="height: 250px;">
                    <canvas id="calorieTrendChart"></canvas>
                </div>
            </div>

            <!-- Macro Distribution with Donut -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Distribuição de Macros
                    </h3>
                    <div class="flex items-center justify-center p-4">
                        <div style="width: 180px; height: 180px;">
                            <canvas id="macroDonutChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6 flex flex-col justify-center">
                    <div class="space-y-6">
                        @foreach([
                            ['label' => 'Proteínas', 'val' => $macroTargets['p'], 'color' => '#fb7185', 'icon' => 'P'],
                            ['label' => 'Carbo', 'val' => $macroTargets['c'], 'color' => '#60a5fa', 'icon' => 'C'],
                            ['label' => 'Gorduras', 'val' => $macroTargets['f'], 'color' => '#fbbf24', 'icon' => 'G']
                        ] as $m)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded bg-zinc-950 flex items-center justify-center text-[10px] font-black" style="color: {{ $m['color'] }}; border: 1px solid {{ $m['color'] }}40;">{{ $m['icon'] }}</span>
                                <span class="text-xs font-bold text-zinc-400">{{ $m['label'] }}</span>
                            </div>
                            <span class="text-sm font-black text-white">{{ $m['val'] ?? '—' }}g</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 4: Water & Insights -->
        <div class="space-y-8">
            <!-- Water Intake -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Hidratação
                </h3>
                <div class="relative flex flex-col items-center">
                    @php 
                        $waterPerc = (int)min(($waterToday / ($waterTarget ?: 2500)) * 100, 100);
                    @endphp
                    <div class="w-32 h-32 rounded-full border-4 border-zinc-800 flex flex-col items-center justify-center overflow-hidden relative">
                        <div class="absolute bottom-0 left-0 w-full transition-all duration-1000 bg-blue-500/40" style="height: {{ $waterPerc }}%"></div>
                        <span class="relative z-10 text-xl font-black text-white">{{ number_format($waterToday / 1000, 1) }}L</span>
                        <span class="relative z-10 text-[10px] text-zinc-500 font-bold uppercase">de {{ number_format($waterTarget / 1000, 1) }}L</span>
                    </div>
                    <div class="flex gap-2 mt-6 w-full">
                        <button @click="addWaterInHub(250)" class="flex-1 py-3 bg-blue-600/10 hover:bg-blue-600 hover:text-white border border-blue-500/20 rounded-2xl text-[10px] font-black text-blue-400 transition-all active:scale-95">
                            +250ml
                        </button>
                        <button @click="addWaterInHub(500)" class="flex-1 py-3 bg-blue-600/10 hover:bg-blue-600 hover:text-white border border-blue-500/20 rounded-2xl text-[10px] font-black text-blue-400 transition-all active:scale-95">
                            +500ml
                        </button>
                    </div>
                </div>
            </div>

            <!-- Insights Card -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    Insights Nutricionais
                </h3>
                <div class="space-y-4">
                    @php
                        $diffAverages = ($averages->cal ?? 0) - $targetKcal;
                    @endphp
                    <div class="flex gap-3">
                        <i class="fas fa-lightbulb text-purple-400 mt-1"></i>
                        <p class="text-xs text-zinc-400">
                            @if(abs($diffAverages) < 100)
                                Balanço excelente! Você está quase idêntico à sua meta.
                            @elseif($diffAverages > 0)
                                Consumo médio está {{ number_format($diffAverages) }} kcal acima da meta. 
                            @else
                                Consumo médio está {{ number_format(abs($diffAverages)) }} kcal abaixo da meta.
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                        <p class="text-xs text-zinc-400">
                            @if(in_array($currentGoal, ['lose', 'lose_aggressive']))
                                Priorize alimentos com baixa densidade calórica e alto volume.
                            @elseif($currentGoal == 'gain')
                                Garanta superávit e bom aporte de proteínas para hipertrofia.
                            @elseif($currentGoal == 'recomp')
                                Foco em proteínas altas e treinos intensos para recomposição.
                            @elseif($currentGoal == 'performance')
                                Garanta carboidratos suficientes para suportar sua carga de treino.
                            @else
                                Mantenha a consistência e foco na densidade nutricional.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NexShape Advanced Ecosystem -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- NexShape AI: Meal Suggester -->
        <div class="bg-gradient-to-br from-blue-600/10 to-purple-600/10 border border-white/5 rounded-[2.5rem] p-8 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-3">
                        <span class="p-2 bg-blue-500 rounded-lg shadow-lg shadow-blue-500/20">
                            <i class="fas fa-robot text-xs text-white"></i>
                        </span>
                        NexShape AI — Sugestão Inteligente
                    </h3>
                    <span class="px-3 py-1 bg-white/5 rounded-full text-[10px] font-black text-blue-400 uppercase tracking-widest border border-white/5">Beta</span>
                </div>

                <div class="space-y-6">
                    <p class="text-zinc-400 text-sm leading-relaxed">
                        Detectamos que você ainda tem <strong>{{ number_format($remaining->cal) }} kcal</strong> disponíveis para hoje. 
                        Para otimizar seu resultado, sugerimos a ingestão de:
                    </p>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-zinc-950/40 p-4 rounded-2xl border border-white/5">
                            <span class="text-[10px] text-zinc-500 font-bold uppercase block mb-1">Proteínas</span>
                            <p class="text-xl font-black text-rose-400">{{ number_format($remaining->p) }}g</p>
                        </div>
                        <div class="bg-zinc-950/40 p-4 rounded-2xl border border-white/5">
                            <span class="text-[10px] text-zinc-500 font-bold uppercase block mb-1">Carbos</span>
                            <p class="text-xl font-black text-blue-400">{{ number_format($remaining->c) }}g</p>
                        </div>
                        <div class="bg-zinc-950/40 p-4 rounded-2xl border border-white/5">
                            <span class="text-[10px] text-zinc-500 font-bold uppercase block mb-1">Gorduras</span>
                            <p class="text-xl font-black text-amber-400">{{ number_format($remaining->f) }}g</p>
                        </div>
                    </div>

                    <button @click="generateMeal()" 
                            class="w-full py-4 bg-white text-zinc-950 font-black rounded-2xl hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-white/5 flex items-center justify-center gap-3">
                        <span x-show="!loadingMeal">Gerar Cardápio Sugerido</span>
                        <span x-show="loadingMeal" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Processando...
                        </span>
                        <i x-show="!loadingMeal" class="fas fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($tab === 'stacks')
        <!-- Smart Stack — Suplementação Evoluída -->
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-emerald-500 rounded-lg shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-layer-group text-xs text-zinc-900"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider">Smart Stack</h3>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">Suplementação Inteligente</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="getAISuggestion()" class="px-3 py-1.5 bg-blue-600/10 text-blue-400 text-[10px] font-black uppercase rounded-xl border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                        <i class="fas fa-magic"></i> IA
                    </button>
                    <button @click="stackModalOpen = true" class="w-8 h-8 flex items-center justify-center bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition-all">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                @forelse($stacks as $stack)
                <div class="bg-zinc-950/40 border border-white/5 rounded-3xl overflow-hidden group/stack">
                    <div class="p-5 flex items-center justify-between border-b border-white/5 bg-gradient-to-r from-emerald-500/5 to-transparent">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                                <i class="fas fa-box-tissue"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-white">{{ $stack->name }}</h4>
                                <div class="flex items-center gap-3 mt-0.5">
                                    <span class="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase rounded">{{ $stack->goal ?? 'Saúde' }}</span>
                                    <span class="text-[9px] text-zinc-500 font-bold uppercase">{{ $stack->supplements->count() }} itens</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right mr-2">
                                <p class="text-[9px] text-zinc-600 font-black uppercase">Adesão</p>
                                <p class="text-xs font-black text-white">{{ $stack->adherence_rate }}%</p>
                            </div>
                            <button @click="selectedStackId = {{ $stack->id }}; supplementModalOpen = true; supplementSearch = ''; showCatalog = false" title="Adicionar Suplemento" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 text-zinc-500 hover:text-white transition-all">
                                <i class="fas fa-plus text-[10px]"></i>
                            </button>
                            <button @click='selectedStackId = {{ $stack->id }}; selectedStack = {!! json_encode($stack, JSON_HEX_APOS) !!}; editStackModalOpen = true' title="Editar Stack" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 text-zinc-500 hover:text-blue-400 transition-all">
                                <i class="fas fa-edit text-[10px]"></i>
                            </button>
                            <form method="POST" action="{{ route('smart-stacks.destroy', $stack->id) }}" class="inline" onsubmit="return confirm('Deseja excluir permanentemente este Smart Stack?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Excluir Stack" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 text-zinc-500 hover:text-rose-500 transition-all">
                                    <i class="fas fa-trash text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="p-2 space-y-2">
                        @foreach($stack->supplements as $sup)
                        <div class="flex items-center justify-between p-3 bg-white/2 hover:bg-white/5 rounded-2xl transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 flex items-center justify-center">
                                    <i class="fas fa-prescription-bottle text-[10px] text-zinc-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">{{ $sup->name }}</p>
                                    <p class="text-[9px] text-zinc-500 uppercase font-black">{{ $sup->dosage }}{{ $sup->unit }} &bull; {{ $sup->time_of_day }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="takeSupplement({{ $sup->id }}); $el.classList.add('bg-emerald-500', 'text-zinc-900')" 
                                        class="w-7 h-7 rounded-full border border-white/10 flex items-center justify-center hover:bg-emerald-500 hover:border-emerald-500 text-zinc-500 hover:text-zinc-900 transition-all @if($sup->last_taken_at && $sup->last_taken_at->isToday()) bg-emerald-500 text-zinc-900 border-emerald-500 @endif">
                                    <i class="fas fa-check text-[9px]"></i>
                                </button>
                                <button type="button" 
                                        @click='confirmDelete("{{ route('smart-stacks.remove-supplement', $sup->id) }}", {!! json_encode($sup->name, JSON_HEX_APOS) !!})'
                                        class="w-7 h-7 rounded-full border border-white/10 flex items-center justify-center hover:bg-rose-500 hover:border-rose-500 text-zinc-500 hover:text-white transition-all" title="Remover">
                                    <i class="fas fa-times text-[9px]"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                    @if($supplements->isEmpty())
                    <div class="text-center py-12 border-2 border-dashed border-white/5 rounded-[2.5rem]">
                        <div class="w-16 h-16 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/5">
                            <i class="fas fa-pills text-zinc-800 text-xl"></i>
                        </div>
                        <p class="text-xs text-zinc-500 font-black uppercase tracking-widest">Nenhum stack configurado</p>
                        <p class="text-[10px] text-zinc-700 mt-2 max-w-[200px] mx-auto italic">Otimize sua rotina com Stacks inteligentes para diferentes fases do dia.</p>
                        <button @click="stackModalOpen = true" class="mt-6 px-6 py-2 bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase rounded-xl border border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition-all">Criar Primeiro Stack</button>
                    </div>
                    @endif
                @endforelse

                @if($supplements->isNotEmpty())
                <div class="pt-4 mt-4 border-t border-white/5">
                    <p class="text-[10px] text-zinc-600 font-black uppercase mb-4 pl-1">Suplementos Avulsos</p>
                    <div class="space-y-3">
                        @foreach($supplements as $sup)
                        <div class="flex items-center justify-between p-4 bg-zinc-950/30 rounded-2xl border border-white/5">
                            <div class="flex items-center gap-4">
                                <div class="w-9 h-9 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center">
                                    <i class="fas fa-capsules text-zinc-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">{{ $sup->name }}</p>
                                    <p class="text-[9px] text-zinc-500 uppercase font-black">{{ $sup->dosage }}{{ $sup->unit }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="takeSupplement({{ $sup->id }}); $el.classList.add('bg-emerald-500', 'text-zinc-900')" 
                                        class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center hover:bg-emerald-500 hover:border-emerald-500 text-zinc-500 hover:text-zinc-900 transition-all @if($sup->last_taken_at && $sup->last_taken_at->isToday()) bg-emerald-500 text-zinc-900 border-emerald-500 @endif">
                                    <i class="fas fa-check text-[10px]"></i>
                                </button>
                                <button type="button" 
                                        @click='confirmDelete("{{ route('supplements.destroy', $sup->id) }}", {!! json_encode($sup->name, JSON_HEX_APOS) !!})'
                                        class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center hover:bg-rose-500 hover:border-rose-500 text-zinc-500 hover:text-white transition-all" title="Remover">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    @endif

    @if($tab === 'diary')
        <!-- Diary Tab Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start pb-20 animate-fade-in">
        <!-- Date Navigation Header -->
        <div class="lg:col-span-12 flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
             <div>
                <h2 class="text-xl font-black text-white tracking-tight">Timeline Alimentar</h2>
                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Registros de {{ date('d/m/Y', strtotime($date)) }}</p>
             </div>

            <div class="flex items-center gap-4">
                 <div class="text-right">
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Saldo do Dia</p>
                    <p class="text-lg font-black {{ ($targetKcal - ($selectedDateSums->cal ?? 0)) < 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                        {{ number_format($targetKcal - ($selectedDateSums->cal ?? 0), 0) }} kcal
                    </p>
                 </div>
                 <div class="h-10 w-px bg-white/5"></div>
                 <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">{{ number_format($selectedDateSums->p ?? 0, 0) }}g P</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">{{ number_format($selectedDateSums->c ?? 0, 0) }}g C</span>
                    </div>
                 </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="lg:col-span-8 space-y-8">
            @foreach(['breakfast', 'lunch', 'dinner', 'snack', 'other'] as $mtype)
                @php
                    $mealRows = $diaryRows->where('meal_type', $mtype);
                    $mealCal = $mealRows->sum('calories');
                    $icons = ['breakfast' => '☀️', 'lunch' => '🍲', 'dinner' => '🌙', 'snack' => '🍎', 'other' => '☕'];
                @endphp
                <div class="group relative bg-zinc-900/20 border border-white/5 rounded-[2.5rem] overflow-hidden transition-all hover:bg-zinc-900/40">
                    <div class="p-6 flex items-center justify-between border-b border-white/5 bg-gradient-to-r from-blue-900/5 to-transparent">
                        <div class="flex items-center gap-4">
                            <span class="text-2xl">{{ $icons[$mtype] }}</span>
                            <div>
                                <h3 class="text-lg font-black text-white tracking-tight">{{ $mealLabels[$mtype] }}</h3>
                                <div class="flex items-center gap-3 mt-0.5">
                                    <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest">{{ $mealRows->count() }} Itens</span>
                                    <span class="text-[9px] text-blue-400 font-bold uppercase tracking-widest">{{ $mealCal }} Kcal</span>
                                </div>
                            </div>
                        </div>
                        <button @click="openRepeatModal('{{ $mtype }}')" class="mr-6 px-3 py-1.5 bg-blue-600/10 text-blue-400 text-[10px] font-black uppercase rounded-lg border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="fas fa-redo-alt mr-1"></i> Repetir
                        </button>
                    </div>
                    
                    <div class="divide-y divide-white/5">
                        @forelse($mealRows as $row)
                        <div class="p-6 flex items-center justify-between group/item hover:bg-white/5 transition-all">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-white group-hover/item:text-blue-400 transition-colors">{{ $row->food_name }}</h4>
                                <div class="flex items-center gap-3 text-[10px] text-zinc-500 mt-1">
                                    <span>{{ $row->amount }} {{ $row->unit }}</span>
                                    <span class="w-1 h-1 bg-zinc-700 rounded-full"></span>
                                    <span class="font-bold text-zinc-400">{{ $row->calories }} kcal</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover/item:opacity-100 transition-all">
                                <a href="{{ route('nutrition.index', ['tab' => 'diary', 'date' => $date, 'edit' => $row->id]) }}" class="w-8 h-8 bg-zinc-800 text-zinc-500 hover:text-blue-400 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                <form method="POST" action="{{ route('diary') }}" data-confirm-delete>
                                    @csrf
                                    <input type="hidden" name="action" value="delete_food">
                                    <input type="hidden" name="entry_date" value="{{ $date }}">
                                    <input type="hidden" name="food_id" value="{{ $row->id }}">
                                    <button type="submit" class="w-8 h-8 bg-zinc-800 text-zinc-500 hover:text-rose-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="py-10 text-center">
                            <p class="text-zinc-700 text-[10px] font-black uppercase tracking-widest italic">Nenhum registro para esta refeição</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Side Form (MELHORIAS 2, 3, 4, 5, 6, 7) -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-zinc-900/60 border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                <header class="mb-8 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-white">{{ $editRow ? 'Editar Alimento' : 'Adicionar Alimento' }}</h3>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-1">Lançamento Inteligente</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="$dispatch('open-scanner')" title="Escanear Código de Barras" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 hover:text-white transition-all flex items-center justify-center border border-white/5 relative group">
                            <i class="fas fa-barcode"></i>
                            @if(!$isPremium) <i class="fas fa-lock absolute -top-1 -right-1 text-[8px] text-amber-500 bg-zinc-900 p-0.5 rounded-full"></i> @endif
                        </button>
                        <button type="button" @click="$dispatch('open-photo')" title="Registrar por Foto" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 hover:text-white transition-all flex items-center justify-center border border-white/5 relative group">
                            <i class="fas fa-camera"></i>
                            @if(!$isPremium) <i class="fas fa-lock absolute -top-1 -right-1 text-[8px] text-amber-500 bg-zinc-900 p-0.5 rounded-full"></i> @endif
                        </button>
                    </div>
                </header>

                <!-- Natural Language Input (MELHORIA 5) -->
                <div class="mb-6 @if(!$isPremium) opacity-50 pointer-events-none @endif">
                    <div class="relative">
                        <textarea x-model="aiInput" placeholder="Ex: Comi 2 ovos e pão integral..." 
                                  class="w-full bg-zinc-950/30 border border-white/5 rounded-2xl p-4 text-xs text-white outline-none focus:ring-1 focus:ring-purple-500/50 resize-none h-20 transition-all"></textarea>
                        <button @click="processAI()" 
                                class="absolute bottom-3 right-3 p-2 bg-purple-600 text-white rounded-lg text-[10px] font-black uppercase hover:bg-purple-500 transition-all disabled:opacity-50"
                                :disabled="!aiInput || isProcessingAI">
                            <span x-show="!isProcessingAI">Registrar</span>
                            <i x-show="isProcessingAI" class="fas fa-spinner animate-spin"></i>
                        </button>
                    </div>
                    @if(!$isPremium)
                    <div class="mt-2 flex items-center gap-2 text-[9px] text-amber-500 font-bold uppercase tracking-widest">
                        <i class="fas fa-lock"></i> Disponível no plano Pro
                    </div>
                    @endif
                </div>

                <div class="h-px bg-white/5 mb-6"></div>

                <form method="POST" action="{{ route('diary') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if($editRow) <input type="hidden" name="food_edit_id" value="{{ $editRow->id }}"> @endif

                    <div class="space-y-4">
                        <!-- Search with Preview (MELHORIA 3) -->
                        <div class="space-y-1.5 relative">
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest pl-1">Buscar Alimento</label>
                            <input type="text" name="food_name" x-model="searchQuery" @input.debounce.300ms="searchFood()"
                                   placeholder="Digite para buscar..."
                                   class="w-full bg-zinc-950/50 border border-white/5 rounded-xl p-3.5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50" required>
                            
                            <!-- Search Results Preview -->
                            <div x-show="searchResults.length > 0" 
                                 class="absolute z-50 top-full left-0 w-full mt-2 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto animate-fade-in"
                                 @click.away="searchResults = []">
                                <template x-for="product in searchResults" :key="product.code">
                                    <button type="button" @click="selectProduct(product.code)" 
                                            class="w-full p-4 flex items-center justify-between hover:bg-white/5 border-b border-white/5 transition-all text-left">
                                        <div>
                                            <p class="text-xs font-bold text-white" x-text="product.name"></p>
                                            <p class="text-[9px] text-zinc-500 uppercase font-black" x-text="product.brands"></p>
                                        </div>
                                        <i class="fas fa-plus text-[10px] text-blue-500"></i>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Unit Selector & Amount (MELHORIA 2) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest pl-1">Quantidade</label>
                                <input type="number" name="amount" x-model="amount"
                                    class="w-full bg-zinc-950 border border-white/5 rounded-xl p-3.5 text-white text-sm outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest pl-1">Unidade</label>
                                <select name="unit" x-model="unit" class="w-full bg-zinc-950 border border-white/5 rounded-xl p-3.5 text-white text-xs outline-none appearance-none">
                                    <template x-for="(label, key) in unitLabels" :key="key">
                                        <option :value="key" x-text="label"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Real-time Macro Preview (MELHORIA 3) -->
                        <template x-if="selectedFood">
                            <div class="bg-blue-600/5 border border-blue-500/20 rounded-2xl p-4 animate-fade-in">
                                <p class="text-[9px] text-blue-400 font-black uppercase tracking-widest mb-3">Preview Nutricional</p>
                                <div class="grid grid-cols-4 gap-2 text-center">
                                    <div>
                                        <p class="text-xs font-black text-white" x-text="currentMacros.kcal"></p>
                                        <p class="text-[8px] text-zinc-500 uppercase font-bold">Kcal</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-rose-400" x-text="currentMacros.p + 'g'"></p>
                                        <p class="text-[8px] text-zinc-500 uppercase font-bold">Prot</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-blue-400" x-text="currentMacros.c + 'g'"></p>
                                        <p class="text-[8px] text-zinc-500 uppercase font-bold">Carb</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-amber-400" x-text="currentMacros.f + 'g'"></p>
                                        <p class="text-[8px] text-zinc-500 uppercase font-bold">Gord</p>
                                    </div>
                                </div>
                                <!-- Hidden inputs for form submission -->
                                <input type="hidden" name="calories" :value="selectedFood.energy_kcal">
                                <input type="hidden" name="p_g" :value="selectedFood.protein_g">
                                <input type="hidden" name="c_g" :value="selectedFood.carbohydrates_g">
                                <input type="hidden" name="f_g" :value="selectedFood.fat_g">
                                
                                <button type="button" @click="selectedFood = null; searchQuery = ''" class="w-full mt-4 text-[9px] text-zinc-500 font-black uppercase hover:text-white transition-colors">
                                    Limpar Seleção
                                </button>
                            </div>
                        </template>

                        <!-- Manual Inputs (Fallback) -->
                        <template x-if="!selectedFood">
                            <div class="space-y-4 animate-fade-in">
                                 <div class="space-y-1.5">
                                    <label class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest pl-1">Calorias (kcal p/ 100g)</label>
                                    <input type="number" name="calories" value="{{ old('calories', $editRow->calories ?? '') }}" 
                                        class="w-full bg-zinc-950 text-white font-black border border-white/5 rounded-xl p-3.5 outline-none">
                                </div>
                                 <div class="grid grid-cols-3 gap-2">
                                     @foreach([['p', 'Proteína'], ['c', 'Carbo'], ['f', 'Gordura']] as $macro)
                                     <div class="space-y-1.5">
                                         <label class="block text-[9px] text-zinc-600 font-bold uppercase tracking-widest text-center">{{ $macro[1] }}</label>
                                         <input type="number" step="0.1" name="{{ $macro[0] }}_g" value="{{ old($macro[0].'_g', $editRow->{$macro[0].'_g'} ?? '') }}" 
                                                class="w-full bg-zinc-950 border border-white/5 rounded-xl p-2 text-center text-white text-xs outline-none">
                                     </div>
                                     @endforeach
                                 </div>
                            </div>
                        </template>

                        <!-- Meal Type (MELHORIA 11 - Automatic selection) -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest pl-1">Refeição</label>
                            <div class="grid grid-cols-2 gap-2">
                                @php
                                    $currentHour = now()->hour;
                                    $defaultMeal = 'other';
                                    if ($currentHour >= 5 && $currentHour < 10) $defaultMeal = 'breakfast';
                                    elseif ($currentHour >= 11 && $currentHour < 15) $defaultMeal = 'lunch';
                                    elseif ($currentHour >= 15 && $currentHour < 19) $defaultMeal = 'snack';
                                    elseif ($currentHour >= 19 && $currentHour < 23) $defaultMeal = 'dinner';
                                @endphp
                                @foreach($mealLabels as $val => $txt)
                                <label class="cursor-pointer">
                                    <input type="radio" name="meal_type" value="{{ $val }}" class="peer hidden" {{ ($editRow->meal_type ?? $defaultMeal) === $val ? 'checked' : '' }}>
                                    <div @click="loadFavorites('{{ $val }}')" class="py-2 text-center rounded-lg bg-zinc-950 border border-white/5 text-[9px] font-black uppercase text-zinc-500 peer-checked:bg-blue-600 peer-checked:text-white transition-all">
                                        {{ $txt }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Favorites (MELHORIA 4) -->
                        <div class="pt-4 space-y-3" x-init="loadFavorites('{{ $defaultMeal }}')">
                            <div class="flex items-center justify-between">
                                <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest pl-1">⭐ Favoritos do Horário</label>
                                <span x-show="loadingFavorites" class="fas fa-spinner animate-spin text-zinc-500 text-[10px]"></span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="fav in favorites" :key="fav.food_name">
                                    <button type="button" @click="selectedFood = {energy_kcal: fav.calories, protein_g: fav.protein_g, carbohydrates_g: fav.carbs_g, fat_g: fav.fat_g, name: fav.food_name}; searchQuery = fav.food_name; unit = fav.unit || 'un'"
                                            class="px-3 py-1.5 bg-white/5 border border-white/5 rounded-full text-[10px] font-bold text-zinc-400 hover:bg-blue-600/10 hover:text-blue-400 hover:border-blue-500/30 transition-all">
                                        <span x-text="fav.food_name"></span>
                                    </button>
                                </template>
                                <template x-if="favorites.length === 0 && !loadingFavorites">
                                    <p class="text-[9px] text-zinc-700 italic">Continue registrando para ver seus favoritos.</p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-white text-zinc-900 font-black rounded-2xl hover:bg-blue-500 hover:text-white transition-all uppercase text-[10px] tracking-widest shadow-xl active:scale-95">
                            {{ $editRow ? 'Atualizar Alimento' : 'Confirmar Lançamento' }}
                        </button>
                        
                        @if($editRow)
                            <a href="{{ route('nutrition.index', ['tab' => 'diary', 'date' => $date]) }}" class="block text-center mt-4 text-zinc-600 text-[9px] font-black uppercase tracking-widest hover:text-white">Cancelar Edição</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Hydration Card (MELHORIA 10) -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-6">
                 <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.5)]"></span>
                    Hidratação — Controle Água
                </h3>
                <div class="flex flex-col items-center">
                    <p class="text-2xl font-black text-white mb-4">{{ number_format($waterToday / 1000, 1) }}L / <small class="text-zinc-500 text-sm italic">{{ number_format($waterTarget / 1000, 1) }}L</small></p>
                    <div class="flex gap-2 w-full">
                        @foreach([250, 500, 1000] as $ml)
                        <button @click="addWaterInHub({{ $ml }})" class="flex-1 py-3 bg-zinc-950 border border-white/5 rounded-xl text-[10px] font-black text-blue-400 hover:bg-blue-600 hover:text-white transition-all shadow-lg active:scale-95">
                            +{{ $ml >= 1000 ? '1L' : $ml.'ml' }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(!$isPremium)
            <!-- Upgrade Pro Card (MELHORIA 12) -->
            <div class="bg-gradient-to-br from-amber-600/20 to-rose-600/20 border border-amber-500/30 p-6 rounded-[2.5rem] text-center">
                <i class="fas fa-crown text-amber-500 text-2xl mb-3"></i>
                <h4 class="text-sm font-black text-white mb-2">Desbloqueie o Poder da IA</h4>
                <p class="text-[10px] text-zinc-400 mb-4 uppercase font-bold tracking-widest">Scanner, Foto, Voz e Mais</p>
                <a href="{{ route('plano') }}" class="inline-block px-6 py-3 bg-amber-500 text-zinc-900 font-black text-[10px] uppercase rounded-xl hover:bg-amber-400 transition-all">Fazer Upgrade</a>
            </div>
            @endif
        </div>

    </div>
    @endif

    <!-- Modal Strategy (AlpineJS) -->
    <div x-show="goalModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm animate-fade-in"
         style="display: none;"
         @keydown.escape.window="goalModalOpen = false">
        
        <div class="absolute inset-0" @click="goalModalOpen = false"></div>
        
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl animate-dashboard-entry">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h5 class="text-white font-black text-xl">Ajustar Estratégia</h5>
                <button @click="goalModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('nutrition.update-goal') }}" method="POST">
                @csrf
                <div class="p-8 space-y-8">
                    <div class="space-y-4">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Objetivo Primário</label>
                        <select name="goal" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:ring-2 focus:ring-blue-500/30 transition-all appearance-none cursor-pointer">
                            @foreach(\App\Models\UserProfile::getAvailableGoals() as $slug => $data)
                                <option value="{{ $slug }}" {{ $currentGoal == $slug ? 'selected' : '' }}>
                                    {{ $data['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Distribuição de Macros (Split)</label>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach([
                                ['split' => 'cutting', 'label' => '🥩 High Protein (Preservação)', 'desc' => 'Foco em saciedade e massa magra.'],
                                ['split' => 'bulking', 'label' => '🍝 High Carb (Energia)', 'desc' => 'Foco em performance e volume.'],
                                ['split' => 'maintenance', 'label' => '🥗 Equilibrado (Saúde)', 'desc' => 'Proporções padrão NexShape.']
                            ] as $s)
                            <label class="relative group cursor-pointer">
                                <input type="radio" name="split" value="{{ $s['split'] }}" class="peer hidden" {{ $s['split'] == 'maintenance' ? 'checked' : '' }}>
                                <div class="p-4 rounded-2xl bg-zinc-950 border border-white/5 peer-checked:bg-blue-600 peer-checked:border-blue-500 transition-all group-hover:border-white/20">
                                    <p class="text-white text-sm font-black peer-checked:text-white transition-colors">{{ $s['label'] }}</p>
                                    <p class="text-[10px] text-zinc-500 mt-1 peer-checked:text-blue-200 transition-colors">{{ $s['desc'] }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-zinc-950/50 border-t border-white/5 flex flex-col md:flex-row gap-4">
                    <button type="button" @click="goalModalOpen = false" class="flex-1 py-4 bg-zinc-800 text-zinc-400 font-black rounded-2xl hover:bg-zinc-700 transition-all uppercase text-[10px] tracking-widest">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/10 uppercase text-[10px] tracking-widest">
                        Salvar Estratégia
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Add Supplement -->
    <div x-show="supplementModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm animate-fade-in"
         style="display: none;"
         @keydown.escape.window="supplementModalOpen = false">
        
        <div class="absolute inset-0" @click="supplementModalOpen = false"></div>
        
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl animate-dashboard-entry">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h5 class="text-white font-black text-xl">Novo Suplemento</h5>
                <button @click="supplementModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('supplements.store') }}" method="POST">
                @csrf
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Nome do Suplemento</label>
                        <input type="text" name="name" required placeholder="Ex: Creatina Creapure" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Dose</label>
                            <input type="text" name="dosage" placeholder="Ex: 5" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Unidade</label>
                            <select name="unit" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none appearance-none">
                                <option value="g">Grama (g)</option>
                                <option value="caps">Cápsula</option>
                                <option value="ml">ML</option>
                                <option value="scoop">Scoop</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Momento do Dia</label>
                        <input type="text" name="time_of_day" placeholder="Ex: Pós-treino ou 08:00" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all">
                    </div>
                </div>

                <div class="p-8 bg-zinc-950/50 border-t border-white/5 flex gap-4">
                    <button type="submit" class="w-full py-4 bg-emerald-500 text-zinc-900 font-black rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 uppercase text-[10px] tracking-widest">
                        Adicionar ao Stack
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Suggestion Result -->
    <div x-show="suggestionModalOpen" 
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-md animate-fade-in"
         style="display: none;"
         @keydown.escape.window="suggestionModalOpen = false">
        
        <div class="absolute inset-0" @click="suggestionModalOpen = false"></div>
        
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-2xl rounded-[2.5rem] overflow-hidden shadow-2xl animate-dashboard-entry">
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-zinc-900/50">
                <h5 class="text-white font-black text-xl flex items-center gap-3">
                    <i class="fas fa-magic text-blue-400"></i>
                    Sugestão NexShape AI
                </h5>
                <button @click="suggestionModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            
            <div class="p-8 max-h-[70vh] overflow-y-auto">
                <div x-show="loadingMeal" class="flex flex-col items-center justify-center py-12 space-y-4">
                    <div class="w-12 h-12 border-4 border-blue-500/20 border-t-blue-500 rounded-full animate-spin"></div>
                    <p class="text-zinc-500 text-xs font-black uppercase tracking-widest">Consultando Especialista IA...</p>
                </div>
                
                <div x-show="!loadingMeal" class="prose prose-invert max-w-none">
                    <div class="bg-zinc-950/50 rounded-3xl p-6 border border-white/5 leading-relaxed text-zinc-300 whitespace-pre-line" x-text="mealSuggestion"></div>
                </div>
            </div>

            <div class="p-8 bg-zinc-950/50 border-t border-white/5 flex gap-4">
                <button @click="suggestionModalOpen = false" class="w-full py-4 bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all uppercase text-[10px] tracking-widest">
                    Fechar
                </button>
                <button @click="adoptSuggestedMeal()" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/10 uppercase text-[10px] tracking-widest">
                    Adotar Refeição
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Audit Result -->
    <div x-show="auditModalOpen" 
         class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="auditModalOpen = false">
        
        <div class="absolute inset-0" @click="auditModalOpen = false"></div>
        
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-3xl rounded-[2.5rem] overflow-hidden shadow-2xl animate-dashboard-entry">
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-purple-600/5">
                <h5 class="text-white font-black text-xl flex items-center gap-3">
                    <i class="fas fa-award text-purple-400"></i>
                    Auditoria Nutricional Semanal
                </h5>
                <button @click="auditModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            
            <div class="p-10 max-h-[70vh] overflow-y-auto">
                <div x-show="loadingAudit" class="flex flex-col items-center justify-center py-12 space-y-6">
                    <div class="relative w-20 h-20">
                        <div class="absolute inset-0 border-4 border-purple-500/10 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-white font-black uppercase text-sm tracking-widest mb-1 animate-pulse">Auditando Histórico</p>
                        <p class="text-zinc-500 text-[10px] uppercase font-bold">Processando dados dos últimos 7 dias...</p>
                    </div>
                </div>
                
                <div x-show="!loadingAudit" class="prose prose-invert max-w-none">
                    <div class="space-y-6">
                        <div class="bg-zinc-950/40 rounded-3xl p-8 border border-white/5 leading-relaxed text-zinc-300 whitespace-pre-line text-sm italic" x-text="auditResult"></div>
                        
                        <div class="p-4 bg-purple-500/10 border border-purple-500/20 rounded-2xl">
                            <p class="text-[10px] text-purple-400 font-black uppercase tracking-widest text-center">Relatório gerado exclusivamente para {{ auth()->user()->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-zinc-950/50 border-t border-white/5">
                <button @click="auditModalOpen = false" class="w-full py-4 bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all uppercase text-[10px] tracking-widest">
                    Entendido, vamos evoluir!
                </button>
            </div>
        </div>
    </div>
    <!-- Modal Repeat Meal (MELHORIA 1) -->
    <div x-show="repeatModalOpen" 
         class="fixed inset-0 z-[130] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="repeatModalOpen = false">
        
        <div class="absolute inset-0" @click="repeatModalOpen = false"></div>
        
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-blue-600/5">
                <h5 class="text-white font-black text-lg flex items-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-redo-alt text-blue-400"></i>
                    Repetir Refeição
                </h5>
                <button @click="repeatModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('nutrition.api.repeat-meal') }}" method="POST">
                @csrf
                <input type="hidden" name="meal_type" :value="repeatMealType">
                <input type="hidden" name="target_date" value="{{ $date }}">
                
                <div class="p-8 space-y-6">
                    <p class="text-xs text-zinc-400">Selecione a origem da refeição para copiar todos os alimentos e macros para hoje.</p>
                    
                    <div class="space-y-3">
                        @foreach([
                            ['source' => 'yesterday', 'label' => 'Ontem', 'icon' => 'calendar-day'],
                            ['source' => 'last', 'label' => 'Última Vez', 'icon' => 'history'],
                            ['source' => 'specific', 'label' => 'Dia Específico', 'icon' => 'calendar-alt']
                        ] as $opt)
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="source" value="{{ $opt['source'] }}" x-model="repeatSource" class="peer hidden">
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-zinc-950 border border-white/5 peer-checked:bg-blue-600 peer-checked:border-blue-500 transition-all">
                                <i class="fas fa-{{ $opt['icon'] }} text-zinc-500 peer-checked:text-white"></i>
                                <span class="text-sm font-bold text-white">{{ $opt['label'] }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="repeatSource === 'specific'" class="space-y-2 animate-fade-in">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Data de Origem</label>
                        <input type="date" name="date" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none">
                    </div>
                </div>

                <div class="p-8 bg-zinc-950/50 border-t border-white/5">
                    <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/10 uppercase text-[10px] tracking-widest">
                        Copiar Refeição
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Criar Smart Stack -->
    <div x-show="stackModalOpen" 
         class="fixed inset-0 z-[140] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="stackModalOpen = false">
        <div class="absolute inset-0" @click="stackModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-emerald-600/5">
                <h5 class="text-white font-black text-lg flex items-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-layer-group text-emerald-400"></i>
                    Novo Smart Stack
                </h5>
                <button @click="stackModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            <form action="{{ route('smart-stacks.store') }}" method="POST">
                @csrf
                <div class="p-8 space-y-5">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Nome do Stack</label>
                        <input type="text" name="name" placeholder="Ex: Protocolo Manhã" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none focus:ring-1 focus:ring-emerald-500/50" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Objetivo</label>
                            <select name="goal" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none appearance-none">
                                <option value="saude">Saúde & Bem-estar</option>
                                <option value="hipertrofia">Hipertrofia</option>
                                <option value="emagrecimento">Emagrecimento</option>
                                <option value="performance">Performance</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Responsável</label>
                            <select name="responsible_type" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none appearance-none">
                                <option value="ia">NexShape AI</option>
                                <option value="profissional">Profissional</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Notas / Instruções</label>
                        <textarea name="notes" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none h-24 resize-none"></textarea>
                    </div>
                </div>
                <div class="p-8 bg-zinc-950/50 border-t border-white/5">
                    <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all uppercase text-[10px] tracking-widest shadow-lg shadow-emerald-500/20">
                        Criar Smart Stack
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Smart Stack -->
    <div x-show="editStackModalOpen" 
         class="fixed inset-0 z-[140] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="editStackModalOpen = false"
         x-cloak>
        <div class="absolute inset-0" @click="editStackModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-blue-600/5">
                <h5 class="text-white font-black text-lg flex items-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-edit text-blue-400"></i>
                    Editar Smart Stack
                </h5>
                <button @click="editStackModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            <form :action="`/smart-stacks/${selectedStackId}`" method="POST">
                @csrf
                @method('PUT')
                <div class="p-8 space-y-5">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Nome do Stack</label>
                        <input type="text" name="name" :value="selectedStack ? selectedStack.name : ''" placeholder="Ex: Protocolo Manhã" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none focus:ring-1 focus:ring-blue-500/50" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Objetivo</label>
                            <select name="goal" :value="selectedStack ? selectedStack.goal : ''" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none appearance-none">
                                <option value="saude">Saúde & Bem-estar</option>
                                <option value="hipertrofia">Hipertrofia</option>
                                <option value="emagrecimento">Emagrecimento</option>
                                <option value="performance">Performance</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Status</label>
                            <select name="status" :value="selectedStack ? selectedStack.status : ''" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none appearance-none">
                                <option value="ativo">Ativo</option>
                                <option value="pausado">Pausado</option>
                                <option value="concluído">Concluído</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Notas / Instruções</label>
                        <textarea name="notes" :value="selectedStack ? selectedStack.notes : ''" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none h-24 resize-none" x-text="selectedStack ? selectedStack.notes : ''"></textarea>
                    </div>
                </div>
                <div class="p-8 bg-zinc-950/50 border-t border-white/5">
                    <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all uppercase text-[10px] tracking-widest shadow-lg shadow-blue-600/20">
                        Atualizar Smart Stack
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar Suplemento (Evoluído) -->
    <div x-show="supplementModalOpen" 
         class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="supplementModalOpen = false">
        <div class="absolute inset-0" @click="supplementModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h5 class="text-white font-black text-lg flex items-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-pills text-emerald-400"></i>
                    Adicionar ao Stack
                </h5>
                <button @click="supplementModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            <form :action="selectedStackId ? `/smart-stacks/${selectedStackId}/supplements` : '{{ route('supplements.store') }}'" method="POST">
                @csrf
                <div class="p-8 space-y-4">
                    <div class="space-y-1.5 relative">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Suplemento / Substância</label>
                        <input type="text" 
                               name="name" 
                               x-model="supplementSearch" 
                               @input.debounce.300ms="searchSupplement()"
                               placeholder="Ex: Creatina Monohidratada" 
                               class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none focus:ring-1 focus:ring-emerald-500/50" 
                               required 
                               autocomplete="off">
                        
                        <!-- Catalog Search Results -->
                        <div x-show="showCatalog" 
                             class="absolute z-50 left-0 right-0 top-full mt-2 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto"
                             style="display: none;"
                             @click.away="showCatalog = false">
                            <template x-for="item in catalogResults" :key="item.id">
                                <button type="button" 
                                        @click="selectFromCatalog(item)"
                                        class="w-full p-4 text-left hover:bg-white/[0.05] border-b border-white/5 last:border-0 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-bold text-white" x-text="item.name"></p>
                                        <span class="text-[9px] text-zinc-500 font-black uppercase" x-text="item.category"></span>
                                    </div>
                                    <p class="text-[9px] text-emerald-500 font-bold mt-1" x-text="item.default_dosage + item.default_unit + ' (Sugestão)'" x-show="item.default_dosage"></p>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Dose</label>
                            <input type="text" name="dosage" placeholder="5" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Unidade</label>
                            <select name="unit" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none appearance-none">
                                <option value="g">g (gramas)</option>
                                <option value="mg">mg</option>
                                <option value="caps">cápsula(s)</option>
                                <option value="ml">ml</option>
                                <option value="scoop">scoop</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Horário</label>
                            <input type="text" name="time_of_day" placeholder="Ex: Pós-treino" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Frequência</label>
                            <select name="frequency" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-sm outline-none appearance-none">
                                <option value="diario">Diário</option>
                                <option value="pre_treino">Pré-Treino</option>
                                <option value="pos_treino">Pós-Treino</option>
                                <option value="em_jejum">Em Jejum</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-8 bg-zinc-950/50 border-t border-white/5">
                    <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all uppercase text-[10px] tracking-widest">
                        Salvar Suplemento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Sugestão IA -->
    <div x-show="aiStackModalOpen" 
         class="fixed inset-0 z-[160] flex items-center justify-center p-4 bg-zinc-950/95 backdrop-blur-3xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="aiStackModalOpen = false">
        <div class="absolute inset-0" @click="aiStackModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-blue-600/5">
                <h5 class="text-white font-black text-xl flex items-center gap-3">
                    <i class="fas fa-magic text-blue-400"></i>
                    NexShape AI Recommendation
                </h5>
                <button @click="aiStackModalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>
            
            <div class="p-10 max-h-[60vh] overflow-y-auto">
                <div x-show="aiStackLoading" class="flex flex-col items-center justify-center py-12">
                    <div class="w-16 h-16 border-4 border-blue-500/10 border-t-blue-500 rounded-full animate-spin mb-6"></div>
                    <p class="text-white font-black uppercase text-xs tracking-[0.3em] animate-pulse">Cruzando dados de Biohacking...</p>
                </div>

                <div x-show="!aiStackLoading && aiStackSuggestion" class="space-y-8 animate-fade-in">
                    <div class="text-center">
                        <h4 class="text-2xl font-black text-white italic" x-text="aiStackSuggestion ? aiStackSuggestion.stack_name : ''"></h4>
                        <p class="text-blue-400 text-[10px] font-black uppercase tracking-widest mt-2" x-text="aiStackSuggestion ? 'Objetivo: ' + aiStackSuggestion.goal : ''"></p>
                    </div>

                    <div class="space-y-4">
                        <template x-for="sup in (aiStackSuggestion ? aiStackSuggestion.supplements : [])" :key="sup.name">
                            <div class="p-6 bg-zinc-950/50 border border-white/5 rounded-[2rem] group hover:border-blue-500/30 transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <h5 class="text-white font-black text-sm" x-text="sup.name"></h5>
                                    <span class="px-2 py-1 bg-blue-500/10 text-blue-400 text-[9px] font-black rounded uppercase" x-text="sup.dosage + sup.unit"></span>
                                </div>
                                <p class="text-xs text-zinc-500 leading-relaxed" x-text="sup.observations"></p>
                                <div class="flex gap-4 mt-4">
                                    <span class="text-[9px] text-zinc-600 font-bold uppercase" x-text="'Horário: ' + sup.time_of_day"></span>
                                    <span class="text-[9px] text-zinc-600 font-bold uppercase" x-text="'Frequência: ' + sup.frequency"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="p-6 bg-amber-500/5 border border-amber-500/10 rounded-2xl">
                        <p class="text-[10px] text-amber-500/70 font-bold leading-relaxed">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Atenção: Estas sugestões são baseadas em algoritmos de IA. Sempre consulte um profissional de saúde antes de iniciar qualquer suplementação.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-zinc-950/50 border-t border-white/5 flex gap-4">
                <button @click="aiStackModalOpen = false" class="flex-1 py-4 bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all uppercase text-[10px] tracking-widest">
                    Fechar
                </button>
                <button @click="adoptAISuggestion()" class="flex-1 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all uppercase text-[10px] tracking-widest shadow-lg shadow-blue-600/20">
                    Adotar Este Smart Stack
                </button>
            </div>
        </div>
    </div>
    <!-- Modal Confirmação de Exclusão Premium -->
    <div x-show="confirmDeleteModalOpen" 
         class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-zinc-950/95 backdrop-blur-2xl animate-fade-in"
         style="display: none;"
         x-cloak>
        <div class="absolute inset-0" @click="confirmDeleteModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 w-full max-w-sm rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-rose-500/10 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-rose-500/20">
                    <i class="fas fa-trash-alt text-2xl"></i>
                </div>
                <h3 class="text-white font-black text-xl mb-2">Confirmar Exclusão</h3>
                <p class="text-zinc-400 text-sm leading-relaxed mb-8">
                    Tem certeza que deseja remover <strong class="text-white italic" x-text="deleteItemName"></strong> do seu protocolo? Esta ação não pode ser desfeita.
                </p>
                
                <div class="flex flex-col gap-3">
                    <form :action="deleteActionUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-4 bg-rose-600 text-white font-black rounded-2xl hover:bg-rose-500 transition-all uppercase text-[10px] tracking-widest shadow-lg shadow-rose-600/20">
                            Sim, Remover Agora
                        </button>
                    </form>
                    <button @click="confirmDeleteModalOpen = false" class="w-full py-4 bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all uppercase text-[10px] tracking-widest">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Calorie Trend Chart
    const trendEl = document.getElementById('calorieTrendChart');
    if (trendEl) {
        const trendCtx = trendEl.getContext('2d');
        new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($historyData->map(fn($d) => \Carbon\Carbon::parse($d->entry_date)->format('d/m'))) !!},
            datasets: [{
                label: 'Real',
                data: {!! json_encode($historyData->pluck('total_cal')) !!},
                borderColor: '#3b82f6',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                pointRadius: 0,
                pointHoverRadius: 4
            }, {
                label: 'Meta',
                data: Array({{ (int)$historyData->count() }}).fill({{ (int)$targetKcal }}),
                borderColor: 'rgba(255, 255, 255, 0.2)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.02)' },
                    ticks: { color: '#52525b', font: { size: 9 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#52525b', font: { size: 9 } }
                }
            }
        });
    }

    // Macro Donut Chart
    const donutEl = document.getElementById('macroDonutChart');
    if (donutEl) {
        const donutCtx = donutEl.getContext('2d');
        new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Proteínas', 'Carbos', 'Gorduras'],
            datasets: [{
                data: [{{ (int)($macroTargets['p'] ?? 0) }}, {{ (int)($macroTargets['c'] ?? 0) }}, {{ (int)($macroTargets['f'] ?? 0) }}],
                backgroundColor: ['#fb7185', '#60a5fa', '#fbbf24'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false } }
        }
    });
    }

    // addWaterInHub removido daqui (duplicado e agora centralizado no x-data)
</script>

<style>
    body { background-color: #0c0f16; }
    [x-cloak] { display: none !important; }
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
