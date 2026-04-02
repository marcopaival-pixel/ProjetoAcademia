@extends('layouts.admin')

@section('title', 'Configurações Globais')

@section('content')
    <div class="card" style="max-width: 600px;">
        <h2 style="margin-top: 0; font-size: 1.125rem;">Opções do Sistema</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 2rem;">Ajuste parâmetros globais de funcionamento da aplicação.</p>

        <form action="{{ route('admin.settings.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="site_name">Nome da Plataforma</label>
                <input type="text" id="site_name" name="site_name" value="{{ \App\Models\AdminSetting::get('site_name', 'ProjetoAcademia') }}">
            </div>

            <div class="form-group">
                <label for="maintenance_mode">Modo de Manutenção</label>
                <select id="maintenance_mode" name="maintenance_mode">
                    <option value="0" {{ \App\Models\AdminSetting::get('maintenance_mode', '0') == '0' ? 'selected' : '' }}>Desativado (Normal)</option>
                    <option value="1" {{ \App\Models\AdminSetting::get('maintenance_mode', '0') == '1' ? 'selected' : '' }}>Ativado (Apenas Admins)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="max_upload_size">Tamanho Máximo de Upload (MB)</label>
                <input type="number" id="max_upload_size" name="max_upload_size" value="{{ \App\Models\AdminSetting::get('max_upload_size', '5') }}">
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Salvar Alterações Gerais</button>
            </div>
        </form>
    </div>

    <div class="card" style="max-width: 600px;">
        <h2 style="margin-top: 0; font-size: 1.125rem;">Aparência e Marca</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 2rem;">Personalize as cores e a identidade visual do sistema.</p>

        <form action="{{ route('admin.settings.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="accent_color">Cor de Destaque (Accent Color)</label>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="color" id="accent_color" name="accent_color" value="{{ \App\Models\AdminSetting::get('accent_color', '#3d9cf5') }}" style="width: 60px; height: 40px; padding: 2px;">
                    <span style="font-family: monospace;">{{ \App\Models\AdminSetting::get('accent_color', '#3d9cf5') }}</span>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Afeta botões, links e gráficos em todo o site.</p>
            </div>

            <div class="form-group">
                <label for="logo_url">URL do Logo Customizado (Opcional)</label>
                <input type="text" id="logo_url" name="logo_url" value="{{ \App\Models\AdminSetting::get('logo_url', '') }}" placeholder="https://exemplo.com/seu-logo.png">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Deixe vazio para usar o logo padrão do sistema.</p>
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn" style="width: 100%; background: #388bfd; color: white;">Aplicar Identidade Visual</button>
            </div>
        </form>
    </div>

    <div class="card" style="max-width: 600px; border-color: rgba(248, 81, 73, 0.3); background-color: rgba(248, 81, 73, 0.05);">
        <h2 style="margin-top: 0; font-size: 1.125rem; color: #f85149;">Zona de Perigo</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Ações irreversíveis que afetam todo o sistema.</p>
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button class="btn" style="background-color: var(--danger); color: white;">Limpar Todos os Caches</button>
            <button class="btn" style="background-color: transparent; border: 1px solid var(--danger); color: var(--danger);">Reiniciar Serviços</button>
        </div>
    </div>
@endsection
