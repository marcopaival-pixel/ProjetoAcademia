@extends('layouts.app')

@section('title', 'Nova Avaliação Física')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card p-4 p-md-5">
                <h2 class="text-gradient mb-4">Nova Avaliação Física</h2>

                <form action="{{ route('assessments.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Seção: Info Básica -->
                        <div class="col-12">
                            <h5 class="text-primary border-bottom border-secondary pb-2 mb-3">Informações Gerais</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">Data da Avaliação</label>
                                    <input type="date" name="assessment_date" class="form-control glass-input" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">Peso Corporal (kg)</label>
                                    <input type="number" step="0.1" name="weight_kg" class="form-control glass-input" placeholder="0.0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-bold">Gordura (BF %)</label>
                                    <input type="number" step="0.1" name="bf_percent" class="form-control glass-input" placeholder="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-bold">Músculo (%)</label>
                                    <input type="number" step="0.1" name="muscle_percent" class="form-control glass-input" placeholder="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">Enviar para Profissional</label>
                                    <select name="professional_id" class="form-select glass-input">
                                        <option value="">Apenas registro pessoal</option>
                                        @foreach($professionals as $pro)
                                            <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">Pressão Arterial</label>
                                    <input type="text" name="blood_pressure" class="form-control glass-input" placeholder="120/80">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">Freq. Cardíaca (bpm)</label>
                                    <input type="number" name="heart_rate" class="form-control glass-input" placeholder="70">
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Tronco -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom border-secondary pb-2 mb-3">Tronco</h5>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Pescoço (cm)</label>
                                    <input type="number" step="0.1" name="neck" class="form-control glass-input">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Tórax (cm)</label>
                                    <input type="number" step="0.1" name="chest" class="form-control glass-input">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Cintura (cm)</label>
                                    <input type="number" step="0.1" name="waist" class="form-control glass-input">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Abdômen (cm)</label>
                                    <input type="number" step="0.1" name="abdomen" class="form-control glass-input">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Quadril (cm)</label>
                                    <input type="number" step="0.1" name="hips" class="form-control glass-input">
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Membros Superiores -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom border-secondary pb-2 mb-3">Membros Superiores</h5>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Braço Esq. (cm)</label>
                                    <input type="number" step="0.1" name="bicep_l" class="form-control glass-input">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Braço Dir. (cm)</label>
                                    <input type="number" step="0.1" name="bicep_r" class="form-control glass-input">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Antebraço Esq. (cm)</label>
                                    <input type="number" step="0.1" name="forearm_l" class="form-control glass-input">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small fw-bold">Antebraço Dir. (cm)</label>
                                    <input type="number" step="0.1" name="forearm_r" class="form-control glass-input">
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Membros Inferiores -->
                        <div class="col-12">
                            <h5 class="text-primary border-bottom border-secondary pb-2 mb-3">Membros Inferiores</h5>
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <label class="form-label text-muted small fw-bold">Coxa Esq. (cm)</label>
                                    <input type="number" step="0.1" name="thigh_l" class="form-control glass-input">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label text-muted small fw-bold">Coxa Dir. (cm)</label>
                                    <input type="number" step="0.1" name="thigh_r" class="form-control glass-input">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label text-muted small fw-bold">Panturrilha Esq. (cm)</label>
                                    <input type="number" step="0.1" name="calf_l" class="form-control glass-input">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label text-muted small fw-bold">Panturrilha Dir. (cm)</label>
                                    <input type="number" step="0.1" name="calf_r" class="form-control glass-input">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Anotações / Observações</label>
                            <textarea name="notes" rows="3" class="form-control glass-input" placeholder="Sentiu alguma diferença? Novo protocolo?"></textarea>
                        </div>

                        <div class="col-12 pt-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-5">Salvar Avaliação</button>
                                <a href="{{ route('assessments.index') }}" class="btn btn-ghost">Cancelar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
