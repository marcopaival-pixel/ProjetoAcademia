<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="IA Usage (Last 7 Days)" title="Monitoramento de chamadas e custos de IA">
        <x-slot:icon>
            <x-pulse::icons.sparkles />
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        @if ($stats->isEmpty())
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>Agent</x-pulse::th>
                        <x-pulse::th class="text-right">Calls</x-pulse::th>
                        <x-pulse::th class="text-right">Tokens</x-pulse::th>
                        <x-pulse::th class="text-right">Cost (USD)</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($stats as $stat)
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $stat->agent_name }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $stat->agent_name }}">
                                    {{ $stat->agent_name }}
                                </code>
                            </x-pulse::td>
                            <x-pulse::td class="text-right text-gray-700 dark:text-gray-300 font-bold tabular-nums">
                                {{ number_format($stat->total_calls) }}
                            </x-pulse::td>
                            <x-pulse::td class="text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                {{ number_format($stat->total_tokens) }}
                            </x-pulse::td>
                            <x-pulse::td class="text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                ${{ number_format($stat->total_cost, 4) }}
                            </x-pulse::td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="h-2"></tr>
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <x-pulse::td class="font-bold">Total</x-pulse::td>
                        <x-pulse::td class="text-right font-bold">{{ number_format($totals['calls']) }}</x-pulse::td>
                        <x-pulse::td class="text-right font-bold">{{ number_format($totals['tokens']) }}</x-pulse::td>
                        <x-pulse::td class="text-right font-bold">${{ number_format($totals['cost'], 4) }}</x-pulse::td>
                    </tr>
                </tfoot>
            </x-pulse::table>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
