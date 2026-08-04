{{-- Comandos Cursor — reutilizado no drawer e na página do incidente --}}
@php
    $cmds = $cursor ?? [];
@endphp
<div class="space-y-3" x-data="{ copied: '' }">
    <p class="text-[10px] font-black text-violet-400 uppercase tracking-[0.25em]">Exportar para Cursor</p>
    <p class="text-[10px] text-zinc-500 leading-relaxed">Copie o prompt ou os comandos abaixo. O sync grava arquivos em <code class="text-zinc-400">.cursor/bug-surgeon/</code>.</p>

    @foreach([
        'cursor_prompt' => 'Prompt Cursor',
        'fetch_context' => '1. Buscar contexto',
        'submit_analysis' => '2. Enviar análise',
        'export_cursor' => 'Export artisan',
        'sync_cursor_ps1' => 'Sync PowerShell',
    ] as $key => $label)
        @if(!empty($cmds[$key]))
            <div class="bg-zinc-950 border border-white/5 rounded-xl p-3">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[9px] font-black text-zinc-500 uppercase">{{ $label }}</span>
                    <button type="button"
                        @click="navigator.clipboard.writeText(@js($cmds[$key])); copied='{{ $key }}'; setTimeout(() => copied='', 2000)"
                        class="text-[9px] font-black uppercase px-2 py-1 rounded-lg bg-violet-600/20 text-violet-300 hover:bg-violet-600/30">
                        <span x-show="copied !== '{{ $key }}'">Copiar</span>
                        <span x-show="copied === '{{ $key }}'" x-cloak>Copiado!</span>
                    </button>
                </div>
                <code class="block text-[10px] text-zinc-400 font-mono break-all whitespace-pre-wrap">{{ $cmds[$key] }}</code>
            </div>
        @endif
    @endforeach

    @if(!empty($cmds['suggested_branch']))
        <p class="text-[10px] text-zinc-600">Branch sugerida: <code class="text-zinc-400">{{ $cmds['suggested_branch'] }}</code></p>
    @endif
</div>
