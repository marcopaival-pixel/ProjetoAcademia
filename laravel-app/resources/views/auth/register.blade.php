@extends('layouts.app')

@section('title', 'Criar conta')

@section('content')
        <h1>Criar conta</h1>
        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0;padding-left:1.25rem;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" action="{{ route('register') }}" class="card" style="max-width: 28rem;" novalidate>
            @csrf
            <div class="form-group">
                <label for="name">Nome</label>
                <input id="name" name="name" type="text" required autocomplete="name" value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label for="password">Senha (mín. 8 caracteres)</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmar senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
        <p class="muted" style="margin-top: 1rem;"><a href="{{ route('login') }}">Já tenho conta</a></p>
@endsection
