<x-mail::message>
# Bem-vindo à nossa plataforma, {{ $user->name }}!

Olá, ficamos felizes em ter você conosco. Sua conta foi criada com o perfil de: **{{ $roleLabel }}**.

Aqui estão algumas informações importantes para o seu primeiro acesso:

- **E-mail**: {{ $user->email }}
- **Status da Conta**: {{ $user->status === 'active' ? 'Ativa' : 'Pendente' }}

Para começar a explorar as funcionalidades, clique no botão abaixo:

<x-mail::button :url="$url">
Acessar Painel
</x-mail::button>

Se tiver qualquer dúvida, sinta-se à vontade para entrar em contato com o suporte através deste canal.

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
