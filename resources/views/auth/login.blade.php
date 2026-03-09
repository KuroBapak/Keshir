<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Keshir POS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; border-radius: 1rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); padding: 2.5rem; width: 100%; max-width: 400px; }
        .login-card h1 { text-align: center; font-size: 1.75rem; margin-bottom: 0.25rem; color: #1e293b; }
        .login-card p { text-align: center; color: #64748b; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.375rem; font-weight: 600; font-size: 0.875rem; color: #334155; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.9rem; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .btn-login { width: 100%; padding: 0.75rem; background: #2563eb; color: white; border: none; border-radius: 0.5rem; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-login:hover { background: #1d4ed8; }
        .error-msg { background: #fef2f2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.85rem; }
        .attendance-link { text-align: center; margin-top: 1rem; }
        .attendance-link a { color: #2563eb; text-decoration: none; font-size: 0.85rem; }
        .attendance-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>☕ Keshir</h1>
        <p>Masuk ke sistem POS</p>

        @if ($errors->any())
            <div class="error-msg">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control"
                       value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="attendance-link">
            <a href="{{ route('attendance.temp') }}">📋 Halaman Absensi (Sementara)</a>
        </div>
    </div>
</body>
</html>
