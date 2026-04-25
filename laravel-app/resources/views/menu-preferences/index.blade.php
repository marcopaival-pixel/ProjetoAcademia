@extends('layouts.app', ['navCurrent' => 'settings.menus'])

@section('title', 'Personalizar Menus')

@section('content')
@php
    $preferences = \App\Models\UserMenuPreference::where('user_id', auth()->id())->pluck('visible', 'menu_id');
@endphp
<div class="profile-header animate-fade-up" style="margin-bottom: 2rem;">
    <h1 style="margin: 0; font-size: 2.5rem; letter-spacing: -0.02em;">Personalizar Menus</h1>
    <p class="muted" style="margin-top: 0.5rem; font-size: 1.125rem;">Selecione os menus que deseja visualizar no seu painel lateral.</p>
</div>

@if (session('success'))
    <div class="alert alert-success animate-fade-up" style="margin-bottom: 2rem; border-radius: 16px; padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.25rem;">✅</span> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error animate-fade-up" style="margin-bottom: 2rem; border-radius: 16px; padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.25rem;">⚠️</span> {{ session('error') }}
    </div>
@endif

<div class="card animate-fade-up" style="padding: 2.5rem; border-radius: 24px; max-width: 800px; margin: 0 auto;">
    <form action="{{ route('menu.preferences.store') }}" method="POST">
        @csrf
        
        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2.5rem;">
            @foreach ($menus as $menu)
                <label style="display: flex; align-items: center; justify-content: space-between; padding: 1.25rem; background: var(--surface-glass); border-radius: 18px; border: 1px solid var(--border); cursor: {{ $menu->is_required ? 'default' : 'pointer' }}; transition: all 0.2s ease; @if($menu->is_required) opacity: 0.8; @else &:hover { border-color: var(--primary); } @endif">
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: color-mix(in oklab, var(--primary) 10%, transparent); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                            @if($menu->icon)
                                <i class="fas fa-{{ $menu->icon }}" style="font-size: 1.25rem;"></i>
                            @else
                                <i class="fas fa-bars" style="font-size: 1.25rem;"></i>
                            @endif
                        </div>
                        <div>
                            <span style="font-weight: 600; font-size: 1.1rem; display: block;">{{ $menu->label }}</span>
                            @if($menu->is_required)
                                <small style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.05em;">Menu Obrigatório</small>
                            @else
                                <small class="muted" style="font-size: 0.85rem;">Exibir este menu no painel lateral</small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="custom-checkbox">
                        @if($menu->is_required)
                            <input type="checkbox" checked disabled style="width: 24px; height: 24px; accent-color: var(--primary);">
                        @else
                            <input type="checkbox" name="menus[{{ $menu->id }}]" value="1" 
                                @checked($preferences->get($menu->id, true))
                                style="width: 24px; height: 24px; accent-color: var(--primary); cursor: pointer;">
                        @endif
                    </div>
                </label>
            @endforeach
        </div>

        <div style="display: flex; gap: 1.5rem; justify-content: flex-end; flex-wrap: wrap;">
            <button type="button" onclick="document.getElementById('restore-form').submit();" class="btn btn-outline" style="padding: 1rem 2rem; border-radius: 16px; font-weight: 600;">
                Restaurar Padrão
            </button>
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; border-radius: 16px; font-weight: 700; box-shadow: var(--shadow-md);">
                Gravar Preferências
            </button>
        </div>
    </form>

    <form id="restore-form" action="{{ route('menu.preferences.restore') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<div class="animate-fade-up" style="text-align: center; margin-top: 3rem; opacity: 0.6;">
    <p style="font-size: 0.9rem;">
        <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
        As alterações serão refletidas em todos os seus dispositivos.
    </p>
</div>
@endsection
