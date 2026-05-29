@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Histórico de Versões</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ class_basename($entityType) }} #{{ $entityId }}</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
        <div class="space-y-6">
            @foreach($versions as $version)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Versão #{{ $version->version_number }}</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            Alterado por {{ $version->user?->name ?? 'Sistema' }} em {{ $version->created_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            Snapshot de Dados
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($version->snapshot as $key => $value)
                        <div class="border border-gray-100 dark:border-gray-700 p-3 rounded-md">
                            <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $key }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono break-all">
                                {{ is_array($value) ? json_encode($value) : $value }}
                            </dd>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($versions->isEmpty())
        <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhum histórico encontrado</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Não há versões anteriores registradas para este item.</p>
        </div>
        @endif
    </div>
</div>
@endsection
