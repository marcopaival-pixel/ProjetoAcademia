@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Simulador de Negociação
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200" x-data="simulator()">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Controles -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Parâmetros da Proposta</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Selecione o Plano</label>
                            <select x-model="selectedPlanId" @change="updatePlan()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Selecione...</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" data-price="{{ $plan->price }}">{{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Valor Base (R$)</label>
                            <input type="number" x-model.number="basePrice" @input="calculate()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Desconto a Conceder (%) 
                                <span class="text-xs text-gray-500">(Máx: {{ $maxDiscount }}%)</span>
                            </label>
                            <input type="range" x-model.number="discountRate" @input="calculate()" min="0" max="{{ $maxDiscount }}" step="0.5" class="w-full mt-2">
                            <div class="text-right text-sm font-bold text-indigo-600" x-text="discountRate + '%'"></div>
                        </div>
                    </div>

                    <!-- Resultados -->
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Resultado Simulado</h3>
                        
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600">Valor Original:</dt>
                                <dd class="text-sm font-medium text-gray-900" x-text="formatCurrency(basePrice)"></dd>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-red-600">Desconto Aplicado:</dt>
                                <dd class="text-sm font-medium text-red-600" x-text="'- ' + formatCurrency(discountAmount)"></dd>
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                <dt class="text-base font-bold text-gray-900">Valor Final para Clínica:</dt>
                                <dd class="text-lg font-bold text-gray-900" x-text="formatCurrency(finalPrice)"></dd>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <dt class="text-sm text-gray-600">Sua Comissão Estimada ({{ $commissionRate }}%):</dt>
                                <dd class="text-sm font-bold text-green-600" x-text="formatCurrency(commissionAmount)"></dd>
                            </div>

                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600">Receita Líquida Empresa:</dt>
                                <dd class="text-sm font-medium text-gray-900" x-text="formatCurrency(companyRevenue)"></dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <button type="button" @click="generateProposal()" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Gerar Proposta Comercial
                            </button>
                        </div>
                    </div>
                </div>

                <form id="pdfForm" action="{{ route('representative.simulator.pdf') }}" method="POST" target="_blank" class="hidden">
                    @csrf
                    <input type="hidden" name="plan_id" :value="selectedPlanId">
                    <input type="hidden" name="base_price" :value="basePrice">
                    <input type="hidden" name="discount_rate" :value="discountRate">
                    <input type="hidden" name="discount_amount" :value="discountAmount">
                    <input type="hidden" name="final_price" :value="finalPrice">
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function simulator() {
    return {
        selectedPlanId: '',
        basePrice: 0,
        discountRate: 0,
        discountAmount: 0,
        finalPrice: 0,
        commissionRate: {{ $commissionRate }},
        commissionAmount: 0,
        companyRevenue: 0,

        updatePlan() {
            if (this.selectedPlanId) {
                const select = document.querySelector('select[x-model="selectedPlanId"]');
                const option = select.options[select.selectedIndex];
                this.basePrice = parseFloat(option.dataset.price);
            } else {
                this.basePrice = 0;
            }
            this.calculate();
        },

        calculate() {
            if (this.basePrice < 0) this.basePrice = 0;
            
            this.discountAmount = (this.basePrice * this.discountRate) / 100;
            this.finalPrice = this.basePrice - this.discountAmount;
            
            // Comissão é calculada sobre o valor final pago pela clínica
            this.commissionAmount = (this.finalPrice * this.commissionRate) / 100;
            this.companyRevenue = this.finalPrice - this.commissionAmount;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
        },

        generateProposal() {
            if (!this.selectedPlanId) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Atenção',
                        text: 'Selecione um plano antes de gerar a proposta.',
                        icon: 'warning',
                        confirmButtonText: 'Entendi',
                        confirmButtonColor: '#10b981',
                        background: '#18181b',
                        color: '#fff',
                        customClass: {
                            popup: 'border border-zinc-800 rounded-3xl',
                            confirmButton: 'rounded-xl text-xs font-black uppercase tracking-widest'
                        }
                    });
                } else {
                    alert('Selecione um plano antes de gerar a proposta.');
                }
                return;
            }
            
            document.getElementById('pdfForm').submit();
        }
    }
}
</script>
@endpush
@endsection
