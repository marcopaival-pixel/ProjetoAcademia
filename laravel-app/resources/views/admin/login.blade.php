<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ProjetoAcademia</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            color: #f0f6fc;
            margin: 0;
        }

        .login-header p {
            color: #8b949e;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            color: #f0f6fc;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            background-color: #0d1117;
            border: 1px solid #30363d;
            border-radius: 6px;
            color: #f0f6fc;
            box-sizing: border-box;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #388bfd;
            box-shadow: 0 0 0 3px rgba(56, 139, 253, 0.3);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #238636;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 1rem;
        }

        .btn:hover {
            background-color: #2ea043;
        }

        .alert {
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-error {
            background-color: rgba(248, 81, 73, 0.1);
            border: 1px solid rgba(248, 81, 73, 0.2);
            color: #ff7b72;
        }

        .alert-success {
            background-color: rgba(63, 185, 80, 0.1);
            border: 1px solid rgba(63, 185, 80, 0.2);
            color: #3fb950;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('images/logo_Academia.png') }}" style="height: 50px; margin-bottom: 1rem;">
            <h1>Painel Admin</h1>
            <p>Área restrita de gestão</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                Dados de acesso inválidos.
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">E-mail Administrativo</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Palavra-passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Autenticar</button>
        </form>

        <p style="text-align: center; font-size: 0.75rem; margin-top: 2rem;">
            <a href="{{ url('/') }}" style="color: #8b949e; text-decoration: none;">&larr; Voltar ao Site</a>
        </p>
    </div>
</body>
</html>
