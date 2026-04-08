@extends('layouts.app')

@push('styles')
<style>
/* ===== Panel ===== */
.glass-panel {
    background: rgba(15, 28, 50, 0.55);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}

/* ===== Botões de alternância Frente/Costas ===== */
.view-btn {
    padding: 6px 20px;
    border-radius: 9999px;
    font-size: 0.78rem;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,0.1);
    color: #94a3b8;
    background: rgba(255,255,255,0.04);
    cursor: pointer;
    transition: all 0.2s;
}
.view-btn.active {
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 0 14px rgba(59,130,246,0.4);
}

/* ===== Contentor da imagem corporal ===== */
.body-image-wrap {
    position: relative;
    display: inline-block;
    width: 100%;
    max-width: 420px;
    user-select: none;
}
.body-image-wrap img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 12px;
    filter: drop-shadow(0 0 24px rgba(59,130,246,0.35));
}

/* ===== Overlay de músculo (clicável, transparente) ===== */
.m-zone {
    position: absolute;
    border-radius: 40%;
    cursor: pointer;
    background: transparent;
    border: 1.5px solid transparent;
    transition: background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
}
.m-zone:hover {
    background: rgba(59, 130, 246, 0.22);
    border-color: rgba(96, 165, 250, 0.5);
    box-shadow: 0 0 12px rgba(59,130,246,0.4);
}
.m-zone.is-selected {
    background: rgba(37, 99, 235, 0.38);
    border-color: rgba(147, 197, 253, 0.85);
    box-shadow: 0 0 18px rgba(59,130,246,0.6), inset 0 0 8px rgba(59,130,246,0.2);
}
/* Tooltip label ao hover */
.m-zone::after {
    content: attr(data-label);
    position: absolute;
    bottom: calc(100% + 6px);
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15,28,50,0.95);
    color: #93c5fd;
    font-size: 0.65rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.15s;
    border: 1px solid rgba(59,130,246,0.3);
}
.m-zone:hover::after { opacity: 1; }

/* ===== Tags de seleção ===== */
.tag-selected { animation: scaleIn 0.2s ease-out; }
@keyframes scaleIn {
    from { transform: scale(0.8); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
</style>
@endpush

@section('content')
@php $userSex = $sex ?? 'M'; @endphp
<script>window.__bodySex = '{{ $userSex }}';</script>

<div class="px-4 py-8 max-w-6xl mx-auto" x-data="bodyTargetSelector()">

    {{-- Cabeçalho --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">
            Selecione as Áreas de Treino
        </h1>
        <p class="text-slate-400 mt-2">Clique nas regiões do corpo para marcar o foco do seu plano.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- ===== MAPA DO CORPO ===== --}}
        <div class="glass-panel p-6 rounded-2xl flex flex-col items-center">

            {{-- Toggle Frente / Costas --}}
            <div class="flex gap-2 mb-6">
                <button type="button" class="view-btn" :class="{ active: view === 'front' }" x-on:click="view = 'front'">Frente</button>
                <button type="button" class="view-btn" :class="{ active: view === 'back'  }" x-on:click="view = 'back'">Costas</button>
            </div>

            {{-- ======================= MASCULINO FRENTE ======================= --}}
            <div x-show="sex === 'M' && view === 'front'" x-transition class="body-image-wrap">
                <img src="{{ asset('images/body/male_front.png') }}" alt="Corpo masculino - frente">

                {{-- Trapézio --}}
                <div class="m-zone" data-label="Trapézio" style="top:14%;left:25%;width:50%;height:7%"
                     :class="{'is-selected':isSelected('Trapézio')}" x-on:click="toggle('Trapézio')"></div>
                {{-- Deltóide esq --}}
                <div class="m-zone" data-label="Deltoides" style="top:17%;left:8%;width:17%;height:11%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Deltóide dir --}}
                <div class="m-zone" data-label="Deltoides" style="top:17%;left:75%;width:17%;height:11%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Peito --}}
                <div class="m-zone" data-label="Peito" style="top:22%;left:22%;width:56%;height:12%"
                     :class="{'is-selected':isSelected('Peito')}" x-on:click="toggle('Peito')"></div>
                {{-- Bíceps esq --}}
                <div class="m-zone" data-label="Bíceps" style="top:29%;left:5%;width:13%;height:16%"
                     :class="{'is-selected':isSelected('Bíceps')}" x-on:click="toggle('Bíceps')"></div>
                {{-- Bíceps dir --}}
                <div class="m-zone" data-label="Bíceps" style="top:29%;left:82%;width:13%;height:16%"
                     :class="{'is-selected':isSelected('Bíceps')}" x-on:click="toggle('Bíceps')"></div>
                {{-- Antebraço esq --}}
                <div class="m-zone" data-label="Antebraço" style="top:45%;left:2%;width:11%;height:14%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Antebraço dir --}}
                <div class="m-zone" data-label="Antebraço" style="top:45%;left:87%;width:11%;height:14%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Abdômen --}}
                <div class="m-zone" data-label="Abdômen" style="top:34%;left:28%;width:44%;height:16%"
                     :class="{'is-selected':isSelected('Abdômen')}" x-on:click="toggle('Abdômen')"></div>
                {{-- Oblíquos esq --}}
                <div class="m-zone" data-label="Oblíquos" style="top:35%;left:15%;width:14%;height:14%"
                     :class="{'is-selected':isSelected('Oblíquos')}" x-on:click="toggle('Oblíquos')"></div>
                {{-- Oblíquos dir --}}
                <div class="m-zone" data-label="Oblíquos" style="top:35%;left:71%;width:14%;height:14%"
                     :class="{'is-selected':isSelected('Oblíquos')}" x-on:click="toggle('Oblíquos')"></div>
                {{-- Quadríceps esq --}}
                <div class="m-zone" data-label="Quadríceps" style="top:54%;left:18%;width:24%;height:20%"
                     :class="{'is-selected':isSelected('Quadríceps')}" x-on:click="toggle('Quadríceps')"></div>
                {{-- Quadríceps dir --}}
                <div class="m-zone" data-label="Quadríceps" style="top:54%;left:58%;width:24%;height:20%"
                     :class="{'is-selected':isSelected('Quadríceps')}" x-on:click="toggle('Quadríceps')"></div>
                {{-- Panturrilha esq --}}
                <div class="m-zone" data-label="Panturrilha" style="top:76%;left:20%;width:20%;height:14%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
                {{-- Panturrilha dir --}}
                <div class="m-zone" data-label="Panturrilha" style="top:76%;left:60%;width:20%;height:14%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
            </div>

            {{-- ======================= MASCULINO COSTAS ======================= --}}
            <div x-show="sex === 'M' && view === 'back'" x-transition class="body-image-wrap">
                <img src="{{ asset('images/body/male_back.png') }}" alt="Corpo masculino - costas">

                {{-- Trapézio --}}
                <div class="m-zone" data-label="Trapézio" style="top:15%;left:25%;width:50%;height:12%"
                     :class="{'is-selected':isSelected('Trapézio')}" x-on:click="toggle('Trapézio')"></div>
                {{-- Deltóide posterior esq --}}
                <div class="m-zone" data-label="Deltoides" style="top:18%;left:8%;width:17%;height:10%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Deltóide posterior dir --}}
                <div class="m-zone" data-label="Deltoides" style="top:18%;left:75%;width:17%;height:10%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Tríceps esq --}}
                <div class="m-zone" data-label="Tríceps" style="top:29%;left:5%;width:13%;height:16%"
                     :class="{'is-selected':isSelected('Tríceps')}" x-on:click="toggle('Tríceps')"></div>
                {{-- Tríceps dir --}}
                <div class="m-zone" data-label="Tríceps" style="top:29%;left:82%;width:13%;height:16%"
                     :class="{'is-selected':isSelected('Tríceps')}" x-on:click="toggle('Tríceps')"></div>
                {{-- Antebraço esq --}}
                <div class="m-zone" data-label="Antebraço" style="top:45%;left:2%;width:11%;height:14%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Antebraço dir --}}
                <div class="m-zone" data-label="Antebraço" style="top:45%;left:87%;width:11%;height:14%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Latíssimo esq --}}
                <div class="m-zone" data-label="Latíssimo" style="top:28%;left:14%;width:18%;height:18%"
                     :class="{'is-selected':isSelected('Latíssimo')}" x-on:click="toggle('Latíssimo')"></div>
                {{-- Latíssimo dir --}}
                <div class="m-zone" data-label="Latíssimo" style="top:28%;left:68%;width:18%;height:18%"
                     :class="{'is-selected':isSelected('Latíssimo')}" x-on:click="toggle('Latíssimo')"></div>
                {{-- Lombar --}}
                <div class="m-zone" data-label="Lombar" style="top:40%;left:30%;width:40%;height:10%"
                     :class="{'is-selected':isSelected('Lombar')}" x-on:click="toggle('Lombar')"></div>
                {{-- Glúteos esq --}}
                <div class="m-zone" data-label="Glúteos" style="top:52%;left:19%;width:26%;height:14%"
                     :class="{'is-selected':isSelected('Glúteos')}" x-on:click="toggle('Glúteos')"></div>
                {{-- Glúteos dir --}}
                <div class="m-zone" data-label="Glúteos" style="top:52%;left:55%;width:26%;height:14%"
                     :class="{'is-selected':isSelected('Glúteos')}" x-on:click="toggle('Glúteos')"></div>
                {{-- Isquiotibiais esq --}}
                <div class="m-zone" data-label="Isquiotibiais" style="top:66%;left:18%;width:25%;height:18%"
                     :class="{'is-selected':isSelected('Isquiotibiais')}" x-on:click="toggle('Isquiotibiais')"></div>
                {{-- Isquiotibiais dir --}}
                <div class="m-zone" data-label="Isquiotibiais" style="top:66%;left:57%;width:25%;height:18%"
                     :class="{'is-selected':isSelected('Isquiotibiais')}" x-on:click="toggle('Isquiotibiais')"></div>
                {{-- Panturrilha esq --}}
                <div class="m-zone" data-label="Panturrilha" style="top:84%;left:20%;width:20%;height:11%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
                {{-- Panturrilha dir --}}
                <div class="m-zone" data-label="Panturrilha" style="top:84%;left:60%;width:20%;height:11%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
            </div>

            {{-- ======================= FEMININO FRENTE ======================= --}}
            <div x-show="sex === 'F' && view === 'front'" x-transition class="body-image-wrap">
                <img src="{{ asset('images/body/female_front.png') }}" alt="Corpo feminino - frente">

                {{-- Trapézio --}}
                <div class="m-zone" data-label="Trapézio" style="top:13%;left:26%;width:48%;height:7%"
                     :class="{'is-selected':isSelected('Trapézio')}" x-on:click="toggle('Trapézio')"></div>
                {{-- Deltóide esq --}}
                <div class="m-zone" data-label="Deltoides" style="top:16%;left:10%;width:16%;height:10%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Deltóide dir --}}
                <div class="m-zone" data-label="Deltoides" style="top:16%;left:74%;width:16%;height:10%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Peito --}}
                <div class="m-zone" data-label="Peito" style="top:21%;left:24%;width:52%;height:12%"
                     :class="{'is-selected':isSelected('Peito')}" x-on:click="toggle('Peito')"></div>
                {{-- Bíceps esq --}}
                <div class="m-zone" data-label="Bíceps" style="top:28%;left:7%;width:12%;height:15%"
                     :class="{'is-selected':isSelected('Bíceps')}" x-on:click="toggle('Bíceps')"></div>
                {{-- Bíceps dir --}}
                <div class="m-zone" data-label="Bíceps" style="top:28%;left:81%;width:12%;height:15%"
                     :class="{'is-selected':isSelected('Bíceps')}" x-on:click="toggle('Bíceps')"></div>
                {{-- Antebraço esq --}}
                <div class="m-zone" data-label="Antebraço" style="top:44%;left:3%;width:10%;height:14%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Antebraço dir --}}
                <div class="m-zone" data-label="Antebraço" style="top:44%;left:87%;width:10%;height:14%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Abdômen --}}
                <div class="m-zone" data-label="Abdômen" style="top:33%;left:29%;width:42%;height:15%"
                     :class="{'is-selected':isSelected('Abdômen')}" x-on:click="toggle('Abdômen')"></div>
                {{-- Oblíquos esq --}}
                <div class="m-zone" data-label="Oblíquos" style="top:34%;left:17%;width:13%;height:13%"
                     :class="{'is-selected':isSelected('Oblíquos')}" x-on:click="toggle('Oblíquos')"></div>
                {{-- Oblíquos dir --}}
                <div class="m-zone" data-label="Oblíquos" style="top:34%;left:70%;width:13%;height:13%"
                     :class="{'is-selected':isSelected('Oblíquos')}" x-on:click="toggle('Oblíquos')"></div>
                {{-- Quadríceps esq --}}
                <div class="m-zone" data-label="Quadríceps" style="top:55%;left:19%;width:23%;height:19%"
                     :class="{'is-selected':isSelected('Quadríceps')}" x-on:click="toggle('Quadríceps')"></div>
                {{-- Quadríceps dir --}}
                <div class="m-zone" data-label="Quadríceps" style="top:55%;left:58%;width:23%;height:19%"
                     :class="{'is-selected':isSelected('Quadríceps')}" x-on:click="toggle('Quadríceps')"></div>
                {{-- Panturrilha esq --}}
                <div class="m-zone" data-label="Panturrilha" style="top:76%;left:20%;width:19%;height:13%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
                {{-- Panturrilha dir --}}
                <div class="m-zone" data-label="Panturrilha" style="top:76%;left:61%;width:19%;height:13%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
            </div>

            {{-- ======================= FEMININO COSTAS ======================= --}}
            <div x-show="sex === 'F' && view === 'back'" x-transition class="body-image-wrap">
                <img src="{{ asset('images/body/female_back.png') }}" alt="Corpo feminino - costas">

                {{-- Trapézio --}}
                <div class="m-zone" data-label="Trapézio" style="top:14%;left:24%;width:52%;height:12%"
                     :class="{'is-selected':isSelected('Trapézio')}" x-on:click="toggle('Trapézio')"></div>
                {{-- Deltóide posterior esq --}}
                <div class="m-zone" data-label="Deltoides" style="top:17%;left:9%;width:15%;height:10%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Deltóide posterior dir --}}
                <div class="m-zone" data-label="Deltoides" style="top:17%;left:76%;width:15%;height:10%"
                     :class="{'is-selected':isSelected('Deltoides')}" x-on:click="toggle('Deltoides')"></div>
                {{-- Tríceps esq --}}
                <div class="m-zone" data-label="Tríceps" style="top:28%;left:6%;width:12%;height:15%"
                     :class="{'is-selected':isSelected('Tríceps')}" x-on:click="toggle('Tríceps')"></div>
                {{-- Tríceps dir --}}
                <div class="m-zone" data-label="Tríceps" style="top:28%;left:82%;width:12%;height:15%"
                     :class="{'is-selected':isSelected('Tríceps')}" x-on:click="toggle('Tríceps')"></div>
                {{-- Antebraço esq --}}
                <div class="m-zone" data-label="Antebraço" style="top:44%;left:3%;width:10%;height:13%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Antebraço dir --}}
                <div class="m-zone" data-label="Antebraço" style="top:44%;left:87%;width:10%;height:13%"
                     :class="{'is-selected':isSelected('Antebraço')}" x-on:click="toggle('Antebraço')"></div>
                {{-- Latíssimo esq --}}
                <div class="m-zone" data-label="Latíssimo" style="top:27%;left:15%;width:17%;height:17%"
                     :class="{'is-selected':isSelected('Latíssimo')}" x-on:click="toggle('Latíssimo')"></div>
                {{-- Latíssimo dir --}}
                <div class="m-zone" data-label="Latíssimo" style="top:27%;left:68%;width:17%;height:17%"
                     :class="{'is-selected':isSelected('Latíssimo')}" x-on:click="toggle('Latíssimo')"></div>
                {{-- Lombar --}}
                <div class="m-zone" data-label="Lombar" style="top:40%;left:31%;width:38%;height:10%"
                     :class="{'is-selected':isSelected('Lombar')}" x-on:click="toggle('Lombar')"></div>
                {{-- Glúteos esq --}}
                <div class="m-zone" data-label="Glúteos" style="top:51%;left:20%;width:26%;height:14%"
                     :class="{'is-selected':isSelected('Glúteos')}" x-on:click="toggle('Glúteos')"></div>
                {{-- Glúteos dir --}}
                <div class="m-zone" data-label="Glúteos" style="top:51%;left:54%;width:26%;height:14%"
                     :class="{'is-selected':isSelected('Glúteos')}" x-on:click="toggle('Glúteos')"></div>
                {{-- Isquiotibiais esq --}}
                <div class="m-zone" data-label="Isquiotibiais" style="top:65%;left:19%;width:24%;height:18%"
                     :class="{'is-selected':isSelected('Isquiotibiais')}" x-on:click="toggle('Isquiotibiais')"></div>
                {{-- Isquiotibiais dir --}}
                <div class="m-zone" data-label="Isquiotibiais" style="top:65%;left:57%;width:24%;height:18%"
                     :class="{'is-selected':isSelected('Isquiotibiais')}" x-on:click="toggle('Isquiotibiais')"></div>
                {{-- Panturrilha esq --}}
                <div class="m-zone" data-label="Panturrilha" style="top:83%;left:21%;width:19%;height:11%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
                {{-- Panturrilha dir --}}
                <div class="m-zone" data-label="Panturrilha" style="top:83%;left:60%;width:19%;height:11%"
                     :class="{'is-selected':isSelected('Panturrilha')}" x-on:click="toggle('Panturrilha')"></div>
            </div>

            <p class="text-xs text-slate-500 mt-4 text-center">
                Passe o cursor sobre as regiões e clique para selecionar.
            </p>
        </div>

        {{-- ===== CONTROLES ===== --}}
        <div class="flex flex-col gap-6">

            {{-- Tags --}}
            <div class="glass-panel p-6 rounded-2xl border border-slate-700/50">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Músculos Selecionados
                    <span class="ml-1 text-blue-400 font-bold" x-text="'(' + selected.length + ')'"></span>
                </label>

                <div class="min-h-[64px] p-3 bg-slate-900/50 rounded-xl border border-slate-800 flex flex-wrap gap-2 items-start">
                    <span x-show="selected.length === 0" class="text-slate-500 text-sm italic self-center">
                        Nenhuma área selecionada…
                    </span>
                    <template x-for="item in selected" :key="item">
                        <span class="tag-selected inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                            <span x-text="item"></span>
                            <button x-on:click="remove(item)" type="button"
                                    class="text-blue-400 hover:text-white focus:outline-none leading-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </span>
                    </template>
                </div>

                {{-- Busca manual --}}
                <div class="mt-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="searchQuery"
                           x-on:keydown.enter.prevent="addFromSearch()"
                           class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl py-3 pl-9 pr-4
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                           placeholder="Adicionar outro músculo… (Enter)">
                </div>
            </div>

            {{-- Formulário --}}
            <form action="{{ route('progression.plans.store-target-selection') }}"
                  method="POST" enctype="multipart/form-data"
                  class="flex flex-col gap-6" id="targetForm">
                @csrf
                <input type="hidden" name="targets" x-bind:value="JSON.stringify(selected)">

                <div class="glass-panel p-6 rounded-2xl border border-slate-700/50">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-slate-300">
                            Foto de Referência <span class="text-slate-500">(opcional)</span>
                        </label>
                        <span class="text-xs bg-slate-800 text-slate-400 px-2 py-0.5 rounded">JPG / PNG</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Acompanhe a evolução desta área ao longo do tempo.</p>

                    <label for="dropzone-file"
                           class="flex flex-col items-center justify-center w-full h-28 border-2 border-slate-700
                                  border-dashed rounded-xl cursor-pointer bg-slate-900/50 hover:bg-slate-800
                                  hover:border-slate-500 transition">
                        <svg class="w-7 h-7 mb-2 text-slate-500" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                        </svg>
                        <p class="text-sm text-slate-400">
                            <span class="font-semibold text-blue-400">Clique para anexar</span> ou arraste
                        </p>
                        <input id="dropzone-file" type="file" name="photo" class="hidden" accept="image/png,image/jpeg">
                    </label>
                </div>

                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/40 text-red-400 px-4 py-3 rounded-xl text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <button type="submit"
                        :disabled="selected.length === 0"
                        :class="selected.length === 0
                            ? 'opacity-40 cursor-not-allowed'
                            : 'hover:-translate-y-1 hover:shadow-[0_12px_24px_-8px_rgba(59,130,246,0.5)]'"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold
                               py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                    Continuar para o Plano
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bodyTargetSelector', () => ({
        selected: [],
        searchQuery: '',
        view: 'front',
        sex: (window.__bodySex || 'M').trim(),

        toggle(area) {
            const idx = this.selected.indexOf(area);
            if (idx >= 0) {
                this.selected.splice(idx, 1);
            } else {
                this.selected.push(area);
            }
        },

        remove(area) {
            this.selected = this.selected.filter(i => i !== area);
        },

        isSelected(area) {
            return this.selected.includes(area);
        },

        addFromSearch() {
            const q = this.searchQuery.trim();
            if (q.length > 0 && !this.selected.includes(q)) {
                this.selected.push(q);
                this.searchQuery = '';
            }
        }
    }));
});
</script>
@endpush
