@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('admin.configuration-center.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                        <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('admin.configuration-center.crud.index', $entity->name) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">{{ $entity->display_name }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">{{ isset($record) ? 'Editar' : 'Novo' }} Registro</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ isset($record) ? 'Editar' : 'Novo' }} {{ $entity->display_name }}</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
            <form action="{{ isset($record) ? route('admin.configuration-center.crud.update', [$entity->name, $record->id]) : route('admin.configuration-center.crud.store', $entity->name) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($record))
                    @method('PUT')
                @endif

                <div class="px-4 py-5 sm:p-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @foreach($fields as $field)
                        <div class="{{ $field->type === 'textarea' ? 'sm:col-span-2' : '' }}">
                            <x-configuration-center.input :field="$field" :value="isset($record) ? $record->{$field->name} : null" />
                        </div>
                    @endforeach
                </div>

                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 text-right sm:px-6 rounded-b-lg border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        @if(isset($record))
                            <a href="{{ route('admin.configuration-center.audit.versions', [get_class($record), $record->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-500">Ver histórico de alterações</a>
                        @endif
                    </div>
                    <div class="space-x-3">
                        <a href="{{ route('admin.configuration-center.crud.index', $entity->name) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ isset($record) ? 'Salvar Alterações' : 'Criar Registro' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
