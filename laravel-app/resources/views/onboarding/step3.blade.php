@extends('layouts.onboarding-premium')

@section('title', 'Contato')
@section('step_title', 'Canais de Atendimento')
@section('step_description', 'Como seus clientes e nossa equipe podem entrar em contato com você?')

@section('content')
<form action="{{ route('onboarding-premium.step.save', 3) }}" method="POST" class="space-y-12">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Telefone Fixo -->
        <div class="space-y-3" x-data="{ 
            phone: '{{ old('phone', $company->phone ?? '') }}',
            mask() {
                let v = this.phone.replace(/\D/g, '');
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                v = v.replace(/(\d)(\d{4})$/, '$1-$2');
                this.phone = v.substring(0, 15);
            }
        }">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Telefone Principal</label>
            <div class="relative">
                <i class="fas fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600"></i>
                <input type="text" name="phone" x-model="phone" @input="mask()"
                    placeholder="(00) 0000-0000"
                    class="w-full input-premium pl-14">
            </div>
        </div>

        <!-- WhatsApp -->
        <div class="space-y-3" x-data="{ 
            whatsapp: '{{ old('whatsapp', $company->whatsapp ?? '') }}',
            mask() {
                let v = this.whatsapp.replace(/\D/g, '');
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                v = v.replace(/(\d)(\d{4})$/, '$1-$2');
                this.whatsapp = v.substring(0, 15);
            }
        }">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">WhatsApp</label>
            <div class="relative">
                <i class="fab fa-whatsapp absolute left-5 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                <input type="text" name="whatsapp" x-model="whatsapp" @input="mask()" required
                    placeholder="(00) 90000-0000"
                    class="w-full input-premium pl-14">
            </div>
        </div>

        <!-- E-mail Comercial -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">E-mail Comercial</label>
            <div class="relative">
                <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600"></i>
                <input type="email" name="email" value="{{ old('email', $company->responsible_email ?? '') }}" required
                    placeholder="contato@suaempresa.com"
                    class="w-full input-premium pl-14">
            </div>
        </div>

        <!-- Site / Landing Page -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Site / Landing Page</label>
            <div class="relative">
                <i class="fas fa-globe absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600"></i>
                <input type="url" name="website" value="{{ old('website', $company->website ?? '') }}"
                    placeholder="https://www.seusite.com"
                    class="w-full input-premium pl-14">
            </div>
        </div>

        <!-- Instagram -->
        <div class="md:col-span-2 space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Instagram</label>
            <div class="relative">
                <i class="fab fa-instagram absolute left-5 top-1/2 -translate-y-1/2 text-pink-500"></i>
                <input type="text" name="instagram" value="{{ old('instagram', $company->instagram ?? '') }}"
                    placeholder="@seuusuario"
                    class="w-full input-premium pl-14">
            </div>
        </div>
    </div>

    <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <a href="{{ route('onboarding-premium.step', 2) }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3">
            Continuar para Endereço <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
