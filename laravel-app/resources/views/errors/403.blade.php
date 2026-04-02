<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acesso Negado</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0d1117;
            color: #e6edf3;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .error-card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 16px;
            padding: 3rem 2rem;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .error-code {
            font-family: 'Outfit', sans-serif;
            font-size: 5rem;
            font-weight: 700;
            color: #f85149;
            margin: 0;
            line-height: 1;
        }

        .error-title {
            margin: 1.5rem 0 1rem;
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            color: #fff;
        }

        .error-msg {
            color: #8b949e;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn {
            display: inline-block;
            background-color: #238636;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn:hover {
            background-color: #2ea043;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <span class="icon">🚫</span>
        <h1 class="error-code">403</h1>
        <h2 class="error-title">Acesso Restrito</h2>
        <p class="error-msg">Lamentamos, mas esta área é reservada exclusivamente a administradores do projeto.</p>
        <a href="{{ route('admin.login') }}" class="btn">Autenticar como Administrador</a>
    </div>
</body>
</html>
