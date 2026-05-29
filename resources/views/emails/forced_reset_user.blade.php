<p>Olá, {{ $user->name }}.</p>

<p>Uma nova senha temporária foi gerada para sua conta.</p>

<p><strong>Senha temporária:</strong> {{ $tempPassword }}</p>

<p>No seu próximo acesso, será obrigatório cadastrar uma nova senha.</p>

<p>Acesse: <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>

<p><em>Esta senha expira em 24 horas.</em></p>
