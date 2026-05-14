<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestor de tareas por area COOTRASENA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #2f5a34; color: #1f2937; }
        .wrap { min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr; }
        .visual { display: flex; align-items: center; justify-content: center; padding: 0; background: #2f5a34 ; overflow: hidden; }
        .visual img { width: 70%; height: auto; object-fit: cover; display: block; object-fit: contain;}
        .form-side { display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 420px; background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
        h1 { margin-top: 0; }
        label { display: block; margin: 10px 0 6px; font-size: 13px; font-weight: 700; }
        input, button { width: 100%; box-sizing: border-box; border-radius: 8px; padding: 10px; font-size: 14px; }
        input { border: 1px solid #d1d5db; }
        button { border: none; margin-top: 14px; background: #39a949; color: #fff; font-weight: 700; cursor: pointer; }
        .err { margin-top: 10px; background: #fee2e2; color: #991b1b; border-radius: 8px; padding: 10px; }
        .logo_login { text-align: center; }
        @media (max-width: 900px) {
            .wrap { grid-template-columns: 1fr; }
            .visual { min-height: 1240px; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="visual">
        <img src="{{ asset('images/login-left-banner.png') }}" alt="Banner COOTRASENA">
    </div>

    <div class="form-side">
        <form class="card" method="POST" action="{{ route('login.post') }}">
            <div class="logo_login">
                <img class="brand-logo" src="{{ asset('images/logo-cotrasena.png') }}" alt="COTRASENA" width="200">
            </div>
            <hr>
            <br>
            @csrf
            <h1>Gestor de tareas</h1>
            <p>Inicio de sesion</p>
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            <label for="password">Contrasena</label>
            <input id="password" type="password" name="password" required>
            <button type="submit">Ingresar</button>
            @if($errors->any())
                <div class="err">{{ $errors->first() }}</div>
            @endif
        </form>
    </div>
</div>
</body>
</html>
