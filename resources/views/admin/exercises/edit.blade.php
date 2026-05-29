@extends('layouts.admin')

@section('title', 'Editar Exercício: ' . $exercise->name)

@section('content')
    <div class="card" style="max-width: 600px;">
        <h2 style="margin-top: 0; font-size: 1.125rem;">Atualizar Informações</h2>
        <form action="{{ route('admin.exercises.update', $exercise->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="name">Nome do Exercício</label>
                <input type="text" id="name" name="name" value="{{ old('name', $exercise->name) }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="muscle_group">Grupo Muscular</label>
                    <select id="muscle_group" name="muscle_group" required>
                        <option value="Peito" {{ $exercise->muscle_group == 'Peito' ? 'selected' : '' }}>Peito</option>
                        <option value="Costas" {{ $exercise->muscle_group == 'Costas' ? 'selected' : '' }}>Costas</option>
                        <option value="Pernas" {{ $exercise->muscle_group == 'Pernas' ? 'selected' : '' }}>Pernas</option>
                        <option value="Ombros" {{ $exercise->muscle_group == 'Ombros' ? 'selected' : '' }}>Ombros</option>
                        <option value="Braços" {{ $exercise->muscle_group == 'Braços' ? 'selected' : '' }}>Braços</option>
                        <option value="Abdomen" {{ $exercise->muscle_group == 'Abdomen' ? 'selected' : '' }}>Abdomen</option>
                        <option value="Cardio" {{ $exercise->muscle_group == 'Cardio' ? 'selected' : '' }}>Cardio</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="difficulty">Dificuldade</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="beginner" {{ $exercise->difficulty == 'beginner' ? 'selected' : '' }}>Iniciante</option>
                        <option value="intermediate" {{ $exercise->difficulty == 'intermediate' ? 'selected' : '' }}>Intermediário</option>
                        <option value="advanced" {{ $exercise->difficulty == 'advanced' ? 'selected' : '' }}>Avançado</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="equipment">Equipamento</label>
                <input type="text" id="equipment" name="equipment" value="{{ old('equipment', $exercise->equipment) }}">
            </div>

            <div class="form-group">
                <x-muscle-selector :selectedMuscles="$selectedMuscles" />
            </div>

            <div class="form-group">
                <label for="instructions">Instruções de Execução</label>
                <textarea id="instructions" name="instructions" rows="4">{{ old('instructions', $exercise->instructions) }}</textarea>
            </div>

            <div class="form-group">
                <label for="tips">Dicas (Uma por linha)</label>
                <textarea id="tips" name="tips" rows="4">{{ old('tips', is_array($exercise->tips) ? implode("\n", $exercise->tips) : $exercise->tips) }}</textarea>
            </div>

            <div class="form-group">
                <label for="common_mistakes">Erros Comuns (Um por linha)</label>
                <textarea id="common_mistakes" name="common_mistakes" rows="4">{{ old('common_mistakes', is_array($exercise->common_mistakes) ? implode("\n", $exercise->common_mistakes) : $exercise->common_mistakes) }}</textarea>
            </div>

            <div class="form-group">
                <label for="video_type">Tipo de Vídeo</label>
                <select id="edit_video_type" name="video_type" onchange="toggleVideoFields(this.value, 'edit')" required>
                    <option value="none" {{ $exercise->video_type == 'none' || !$exercise->video_type ? 'selected' : '' }}>Sem Vídeo</option>
                    <option value="youtube" {{ $exercise->video_type == 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="upload" {{ $exercise->video_type == 'upload' ? 'selected' : '' }}>Upload (MP4)</option>
                    <option value="gif" {{ $exercise->video_type == 'gif' ? 'selected' : '' }}>GIF Animado (URL)</option>
                </select>
            </div>

            <div class="form-group" id="edit_video_url_div" style="display: {{ in_array($exercise->video_type, ['youtube', 'gif']) ? 'block' : 'none' }};">
                <label for="video_url">URL do Vídeo</label>
                <input type="text" id="video_url" name="video_url" value="{{ old('video_url', $exercise->video_url) }}">
            </div>

            <div class="form-group" id="edit_video_file_div" style="display: {{ $exercise->video_type == 'upload' ? 'block' : 'none' }};">
                <label for="video_file">Substituir Arquivo de Vídeo</label>
                <input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm,image/gif">
                @if($exercise->video_type == 'upload' && $exercise->video_url)
                    <small style="display:block;margin-top:0.5rem;color:#888;">Vídeo atual salvo. Envie novo arquivo apenas se desejar substituir.</small>
                @endif
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin: 1.5rem 0;">
                <input type="checkbox" id="is_active" name="is_active" {{ $exercise->is_active ? 'checked' : '' }} style="width: auto;">
                <label for="is_active" style="margin: 0;">Disponível no app (Ativo)</label>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar Alterações</button>
                <a href="{{ route('admin.exercises.catalog') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">Voltar</a>
            </div>
        </form>
    </div>
@endsection

<script>
    function toggleVideoFields(type, prefix) {
        const urlDiv = document.getElementById(prefix + '_video_url_div');
        const fileDiv = document.getElementById(prefix + '_video_file_div');
        if (type === 'youtube' || type === 'gif') {
            urlDiv.style.display = 'block';
            fileDiv.style.display = 'none';
        } else if (type === 'upload') {
            urlDiv.style.display = 'none';
            fileDiv.style.display = 'block';
        } else {
            urlDiv.style.display = 'none';
            fileDiv.style.display = 'none';
        }
    }
</script>
