@props([
    'headers' => []
])

<div class="overflow-hidden bg-zinc-900 border border-zinc-800 rounded-[2rem] shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-950/50">
                    @foreach($headers as $header)
                        <th class="px-6 py-5 text-xs font-bold text-zinc-500 uppercase tracking-widest border-b border-zinc-800">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
