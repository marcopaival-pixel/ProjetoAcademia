@extends('layouts.admin')

@section('title', 'Editar Exercício: ' . $exercise->name)

@section('content')
    <div class="card" style="max-width: 600px;">
        <h2 style="margin-top: 0; font-size: 1.125rem;">Atualizar Informações</h2>
        <form action="{{ route('admin.exercises.update', $exercise->id) }}" method="POST">
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
                <label for="instructions">Instruções de Execução</label>
                <textarea id="instructions" name="instructions" rows="4">{{ old('instructions', $exercise->instructions) }}</textarea>
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
