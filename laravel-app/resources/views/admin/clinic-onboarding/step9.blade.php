@extends('layouts.clinic-onboarding')

@section('title', 'Configuração do Prontuário')

@section('content')
<form action="{{ route('admin.clinic-onboarding.step.save', [$company, 9]) }}" method="POST" class="space-y-10">
    @csrf
    
    <div class="space-y-8">
        <h3 class="text-white font-bold text-xl flex items-center">
            <i class="fas fa-file-medical mr-3 text-blue-500"></i> Segurança e Compartilhamento
        </h3>

        <label class="block p-8 bg-white/5 border border-white/5 rounded-3xl cursor-pointer hover:bg-white/10 transition-all group has-[:checked]:border-blue-500/50">
            <div class="flex items-start">
                <div class="w-12 h-12 bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-500 group-hover:text-blue-500 transition-colors mr-6">
                    <i class="fas fa-users-cog text-xl"></i>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between">
                        <span class="text-white font-bold text-lg">Prontuário Compartilhado</span>
                        <input type="checkbox" name="shared_medical_records" value="1" {{ $company->shared_medical_records ? 'checked' : '' }}
                            class="w-6 h-6 rounded-lg bg-zinc-900 border-white/10 text-blue-600 focus:ring-blue-500">
                    </div>
                    <p class="text-zinc-500 text-sm mt-2 leading-relaxed">
                        Quando ativado, todos os profissionais da clínica podem visualizar as evoluções e históricos uns dos outros para o mesmo paciente. 
                        Ideal para clínicas multidisciplinares.
                    </p>
                </div>
            </div>
        </label>
    </div>

    <div class="space-y-8">
        <h3 class="text-white font-bold text-xl flex items-center">
            <i class="fas fa-file-pdf mr-3 text-emerald-500"></i> Documentos e Laudos (PDF)
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-white/5 border border-white/5 rounded-2xl">
                <h4 class="text-white font-bold mb-2">Modelos Disponíveis</h4>
                <p class="text-zinc-500 text-xs mb-4">Selecione os modelos de laudo que sua clínica utilizará.</p>
                <div class="space-y-2">
                    @foreach(['Atestado Médico', 'Evolução Clínica', 'Pedido de Exames', 'Receituário'] as $model)
                    <div class="flex items-center text-xs text-zinc-400">
                        <i class="fas fa-check text-emerald-500 mr-2"></i> {{ $model }}
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 bg-zinc-900/50 border border-white/5 rounded-2xl flex flex-col items-center justify-center text-center">
                <i class="fas fa-magic text-3xl text-zinc-700 mb-4"></i>
                <p class="text-zinc-500 text-xs px-4">Você poderá personalizar o cabeçalho e rodapé dos PDFs após a ativação na aba "PDF Suite".</p>
            </div>
        </div>
    </div>

    <div class="pt-8 border-t border-white/5 flex justify-between">
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 8]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Salvar e Continuar
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </div>
</form>
@endsection
