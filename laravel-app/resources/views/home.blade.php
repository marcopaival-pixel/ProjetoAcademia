@extends('layouts.app')

@section('title', 'Início')

@section('content')
        <h1>ProjetoAcademia</h1>
        <p class="lead">Acompanhe alimentação, exercícios e peso em um só lugar — layout pensado para celular e desktop.</p>
        <div class="actions-inline">
            <a class="btn btn-primary" href="{{ route('login') }}">Entrar</a>
            <a class="btn btn-ghost" href="{{ route('register') }}">Criar conta</a>
        </div>
@endsection
