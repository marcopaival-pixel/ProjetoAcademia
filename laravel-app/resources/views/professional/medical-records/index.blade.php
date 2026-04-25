@extends('professional.medical-records.layout')

@section('medical-content')
<div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-12 shadow-xl text-center">
    <div class="w-24 h-24 bg-blue-500/10 rounded-full flex items-center justify-center mx-auto mb-8 text-blue-500">
        <i class="fas fa-file-medical text-4xl"></i>
    </div>
    
    <h2 class="text-3xl font-black text-white mb-4 tracking-tight">Prontuário de <span class="text-blue-500">{{ $patient->name }}</span></h2>
    <p class="text-zinc-400 font-medium max-w-xl mx-auto mb-10">Utilize o menu acima para navegar entre as seções do prontuário, registrar novos atendimentos, emitir laudos e gerenciar a documentação clínica do paciente.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
        <div class="p-6 bg-zinc-800/50 border border-zinc-800 rounded-3xl text-left">
            <i class="fas fa-notes-medical text-blue-500 text-xl mb-4"></i>
            <h4 class="text-white font-bold mb-2">Evoluções</h4>
            <p class="text-zinc-500 text-xs">Acompanhamento contínuo e registros de cada sessão ou consulta.</p>
        </div>
        <div class="p-6 bg-zinc-800/50 border border-zinc-800 rounded-3xl text-left">
            <i class="fas fa-file-medical-alt text-amber-500 text-xl mb-4"></i>
            <h4 class="text-white font-bold mb-2">Laudos e Pareceres</h4>
            <p class="text-zinc-500 text-xs">Emissão de documentos técnicos e análises aprofundadas.</p>
        </div>
        <div class="p-6 bg-zinc-800/50 border border-zinc-800 rounded-3xl text-left">
            <i class="fas fa-history text-purple-500 text-xl mb-4"></i>
            <h4 class="text-white font-bold mb-2">Segurança e Auditoria</h4>
            <p class="text-zinc-500 text-xs">Histórico completo de todas as alterações realizadas no prontuário.</p>
        </div>
    </div>
</div>
@endsection
