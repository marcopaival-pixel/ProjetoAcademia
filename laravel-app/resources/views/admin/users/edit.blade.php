@extends('layouts.admin')

@section('title', 'Editar Utilizador: ' . $user->name)

@section('content')
    <div class="card" style="max-width: 700px;">
        <h2 style="margin-top: 0;">Ficha do Utilizador #{{ $user->id }}</h2>
        
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div style="margin: 2rem 0; padding: 1.5rem; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid var(--border-color);">
                <h3 style="margin-top: 0; font-size: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Nível de Acesso & Assinatura</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1rem;">
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="is_premium" value="1" {{ $user->is_premium ? 'checked' : '' }} style="width: auto;">
                            <span>Utilizador Premium Active</span>
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Dá acesso ao chat IA ilimitado e exportação de relatórios.</p>
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} style="width: auto;">
                            <span style="color: #f85149;">Acesso root (Administrador)</span>
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Permite aceder a este painel e ver todos os dados do sistema.</p>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label for="premium_expires_at">Expiração do Premium</label>
                    <input type="date" id="premium_expires_at" name="premium_expires_at" value="{{ $user->premium_expires_at ? $user->premium_expires_at->format('Y-m-d') : '' }}">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Deixe vazio para premium vitalício (enquanto a flag estiver ativa).</p>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Atualizar Cadastro</button>
                <a href="{{ route('admin.users') }}" class="btn" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-main);">Cancelar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top: 0;">Informações de Conta</h3>
        <div style="display: flex; gap: 3rem;">
            <div>
                <span style="color: var(--text-muted); font-size: 0.875rem;">Membro desde:</span>
                <p><strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong></p>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.875rem;">Última Atualização:</span>
                <p><strong>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'Nunca' }}</strong></p>
            </div>
        </div>
    </div>
@endsection
