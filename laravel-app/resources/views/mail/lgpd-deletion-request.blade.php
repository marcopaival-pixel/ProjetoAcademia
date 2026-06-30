Novo pedido de exclusão de conta (LGPD).

Utilizador: #{{ $user->id }} — {{ $user->name }}
E-mail: {{ $user->email }}
Motivo informado: {{ $reason ?: 'Não informado' }}
Data/hora: {{ now()->format('d/m/Y H:i:s') }}

Painel admin: {{ $adminUrl }}
