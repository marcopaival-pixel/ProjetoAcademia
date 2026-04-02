@extends('layouts.admin')

@section('title', 'Catálogo de Exercícios')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Formulário de Cadastro -->
        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Novo Exercício</h2>
            <form action="{{ route('admin.exercises.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nome do Exercício</label>
                    <input type="text" id="name" name="name" required placeholder="Ex: Supino Reto">
                </div>

                <div class="form-group">
                    <label for="muscle_group">Grupo Muscular</label>
                    <select id="muscle_group" name="muscle_group" required>
                        <option value="">Selecione...</option>
                        <option value="Peito">Peito</option>
                        <option value="Costas">Costas</option>
                        <option value="Pernas">Pernas</option>
                        <option value="Ombros">Ombros</option>
                        <option value="Braços">Braços</option>
                        <option value="Abdomen">Abdomen</option>
                        <option value="Cardio">Cardio</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="difficulty">Dificuldade</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="beginner">Iniciante</option>
                        <option value="intermediate">Intermediário</option>
                        <option value="advanced">Avançado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="equipment">Equipamento (Opcional)</label>
                    <input type="text" id="equipment" name="equipment" placeholder="Halteres, Barra, etc.">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Cadastrar no Sistema</button>
            </form>
        </div>

        <!-- Lista de Exercícios -->
        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Exercícios Cadastrados</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Grupo</th>
                            <th>Equipamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exercises as $ex)
                            <tr>
                                <td>
                                    <strong>{{ $ex->name }}</strong>
                                    <div><span class="badge {{ $ex->difficulty == 'beginner' ? 'badge-success' : ($ex->difficulty == 'intermediate' ? 'badge-info' : 'badge-warning') }}">{{ ucfirst($ex->difficulty) }}</span></div>
                                </td>
                                <td>{{ $ex->muscle_group }}</td>
                                <td>{{ $ex->equipment ?: '—' }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="{{ route('admin.exercises.edit', $ex->id) }}" class="btn" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border: 1px solid var(--border-color);">Editar</a>
                                        <form action="{{ route('admin.exercises.delete', $ex->id) }}" method="POST" onsubmit="return confirm('Apagar este exercício do catálogo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; color: #f85149; border: 1px solid rgba(248, 81, 73, 0.2);">X</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; padding: 2rem;">Nenhum exercício no catálogo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
