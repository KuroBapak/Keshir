<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Keshir POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated background shapes */
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            animation: float 25s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(30px, -30px) rotate(5deg); }
            50% { transform: translate(0, -50px) rotate(0deg); }
            75% { transform: translate(-30px, -30px) rotate(-5deg); }
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            padding: 3rem 2.5rem;
            animation: slideUp 0.6s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
        }
        .brand h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.025em;
        }
        .brand h1 span {
            color: #2563eb;
        }
        .brand p {
            color: #64748b;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }
        
        .error-msg {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #991b1b;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            border: 1px solid #fecaca;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .error-msg::before {
            content: '⚠️';
            flex-shrink: 0;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: #334155;
        }
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s ease;
            background: #fff;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .form-control::placeholder {
            color: #94a3b8;
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.75rem 0;
            color: #94a3b8;
            font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .divider span {
            padding: 0 1rem;
        }
        
        .attendance-link {
            text-align: center;
        }
        .attendance-link a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #2563eb;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            background: #eff6ff;
        }
        .attendance-link a:hover {
            background: #dbeafe;
            transform: translateY(-1px);
        }
        
        .footer-text {
            text-align: center;
            margin-top: 2rem;
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand">
                <div class="brand-icon">☕</div>
                <h1>Keshir <span>POS</span></h1>
                <p>Masuk ke sistem Point of Sale</p>
            </div>

            @if ($errors->any())
                <div class="error-msg">
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control"
                           value="{{ old('username') }}" placeholder="Masukkan username Anda" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Masukkan password Anda" required>
                </div>
                <button type="submit" class="btn-login">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="divider"><span>atau</span></div>

            <div class="attendance-link">
                <a href="{{ route('attendance.temp') }}">
                    📋 Halaman Absensi Karyawan
                </a>
            </div>
        </div>
        
        <p class="footer-text">© {{ date('Y') }} Keshir Coffee & Eatery. All rights reserved.</p>
    </div>
</body>
</html>
