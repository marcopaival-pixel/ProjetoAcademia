@extends('layouts.admin')

@section('title', 'Editar Depoimento')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Marketing & Vendas</p>
        <h1 class="text-3xl font-black text-white tracking-tight">Editar Depoimento</h1>
    </div>

    <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data" class="bg-[#11141b] border border-white/5 p-8 rounded-3xl space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="space-y-2">
                <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">Nome do Cliente *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $testimonial->name) }}" required
                       class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white uppercase font-bold tracking-wider placeholder:text-zinc-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                @error('name') <span class="text-xs text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
            </div>

            <!-- Profession -->
            <div class="space-y-2">
                <label for="profession" class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">Profissão / Cargo</label>
                <input type="text" id="profession" name="profession" value="{{ old('profession', $testimonial->profession) }}" placeholder="Ex: Personal Trainer, Médico, Gestor"
                       class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white uppercase font-bold tracking-wider placeholder:text-zinc-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                @error('profession') <span class="text-xs text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <!-- City -->
            <div class="col-span-2 space-y-2">
                <label for="city" class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">Cidade</label>
                <input type="text" id="city" name="city" value="{{ old('city', $testimonial->city) }}"
                       class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white uppercase font-bold tracking-wider placeholder:text-zinc-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                @error('city') <span class="text-xs text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
            </div>

            <!-- State -->
            <div class="space-y-2">
                <label for="state" class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">UF / Estado</label>
                <input type="text" id="state" name="state" value="{{ old('state', $testimonial->state) }}" max="2" placeholder="Ex: SP"
                       class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white uppercase font-bold tracking-wider placeholder:text-zinc-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                @error('state') <span class="text-xs text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Rating -->
            <div class="space-y-2">
                <label for="rating" class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">Nota (1 a 5 Estrelas) *</label>
                <select id="rating" name="rating" required
                        class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:outline-none focus:border-emerald-500/50">
                    <option value="5" {{ old('rating', $testimonial->rating) == '5' ? 'selected' : '' }}>5 Estrelas ★★★★★</option>
                    <option value="4" {{ old('rating', $testimonial->rating) == '4' ? 'selected' : '' }}>4 Estrelas ★★★★</option>
                    <option value="3" {{ old('rating', $testimonial->rating) == '3' ? 'selected' : '' }}>3 Estrelas ★★★</option>
                    <option value="2" {{ old('rating', $testimonial->rating) == '2' ? 'selected' : '' }}>2 Estrelas ★★</option>
                    <option value="1" {{ old('rating', $testimonial->rating) == '1' ? 'selected' : '' }}>1 Estrela ★</option>
                </select>
                @error('rating') <span class="text-xs text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
            </div>

            <!-- Avatar -->
            <div class="space-y-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">Foto Atual do Cliente</label>
                <div class="flex items-center gap-4">
                    <img src="{{ $testimonial->avatar_url }}" alt="{{ $testimonial->name }}" class="w-12 h-12 rounded-xl object-cover border border-white/10">
                    <input type="file" id="avatar" name="avatar" accept="image/*"
                           class="flex-1 bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-zinc-400 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all">
                </div>
                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-wider mt-1">Formatos sugeridos: JPG, PNG. Tamanho máximo: 1MB. Deixe em branco para manter a atual.</p>
                @error('avatar') <span class="text-xs text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Testimonial Text -->
        <div class="space-y-2">
            <label for="testimonial" class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">Depoimento *</label>
            <textarea id="testimonial" name="testimonial" rows="4" required maxlength="1000"
                      class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white font-medium placeholder:text-zinc-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all">{{ old('testimonial', $testimonial->testimonial) }}</textarea>
            @error('testimonial') <span class="text-xs text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
        </div>

        <!-- Checkboxes -->
        <div class="flex flex-wrap gap-6 pt-4 border-t border-white/5">
            <!-- Featured -->
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="featured" value="1" {{ old('featured', $testimonial->featured) ? 'checked' : '' }}
                       class="w-4 h-4 rounded bg-zinc-950 border-white/10 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-zinc-950">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-300">Destacar Depoimento</span>
            </label>

            <!-- Is Public -->
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_public" value="1" {{ old('is_public', $testimonial->is_public) ? 'checked' : '' }}
                       class="w-4 h-4 rounded bg-zinc-950 border-white/10 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-zinc-950">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-300">Exibir Publicamente</span>
            </label>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 pt-4 justify-end">
            <a href="{{ route('admin.testimonials.index') }}" class="px-6 py-3.5 rounded-xl bg-zinc-900 border border-white/10 hover:bg-zinc-800 text-zinc-400 text-xs font-black uppercase tracking-wider transition-all">Cancelar</a>
            <button type="submit" class="px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-emerald-600/25">Atualizar Depoimento</button>
        </div>
    </form>
</div>
@endsection
