@extends('layouts.app')

@section('title', 'Criar conta')

@section('content')
    <div class="auth-page">
        <h1>Criar sua conta</h1>
        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0;padding-left:1.25rem;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" action="{{ route('register') }}" class="card" novalidate>
            @csrf
            <div class="form-group">
                <label for="name">Nome completo</label>
                <input id="name" name="name" type="text" required autocomplete="name" value="{{ old('name') }}" placeholder="Como deseja ser chamado?">
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" placeholder="exemplo@email.com">
            </div>
            <div class="form-group">
                <label for="password">Senha (mín. 8 caracteres)</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmar senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary w-full">Cadastrar agora</button>
        </form>
        <p class="muted" style="margin-top: 1.5rem;"><a href="{{ route('login') }}">Já tem uma conta? <strong>Fazer login</strong></a></p>
    </div>
@endsection
