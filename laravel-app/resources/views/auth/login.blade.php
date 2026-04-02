@extends('layouts.app')

@section('title', 'Entrar')

@section('content')
    <div class="auth-page">
        <h1>Entrar</h1>
        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('login') }}" class="card" novalidate>
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary w-full">Entrar</button>
        </form>
        <p class="muted" style="margin-top: 1.5rem;"><a href="{{ route('register') }}">Não tem uma conta? <strong>Criar agora</strong></a></p>
    </div>
@endsection
