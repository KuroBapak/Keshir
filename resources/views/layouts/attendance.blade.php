<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absensi — Keshir')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --primary-darker: #1e40af;
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-200: #bfdbfe;
            --accent: #06b6d4;
            --accent-light: #22d3ee;
            --bg: #f8fafc;
            --bg-dark: #f1f5f9;
            --card: #ffffff;
            --text: #0f172a;
            --text-secondary: #475569;
            --muted: #64748b;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --success: #10b981;
            --success-bg: #d1fae5;
            --danger: #ef4444;
            --danger-bg: #fee2e2;
            --warning: #f59e0b;
            --warning-bg: #fef3c7;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius: 0.75rem;
            --radius-lg: 1rem;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Standalone Top Bar */
        .attendance-topbar {
            background: linear-gradient(135deg, #1e3a8a 0%, var(--primary) 35%, var(--accent) 100%);
            color: #fff;
            padding: 0 1.5rem;
            height: 72px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 30px rgba(37, 99, 235, 0.35);
            overflow: hidden;
        }
        .attendance-topbar::before {
            content: '';
            position: absolute;
            top: 0; left: -50%;
            width: 200%; height: 100%;
            background: linear-gradient(90deg,
                transparent 0%,
                rgba(255,255,255,0.1) 25%,
                rgba(255,255,255,0.2) 50%,
                rgba(255,255,255,0.1) 75%,
                transparent 100%);
            animation: shimmer 8s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes shimmer {
            0%, 100% { transform: translateX(-25%); }
            50% { transform: translateX(25%); }
        }
        .attendance-topbar::after {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            position: relative;
            z-index: 1;
        }
        .topbar-brand-icon {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.1) 100%);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .topbar-brand-text {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .topbar-brand-text span {
            font-weight: 400;
            opacity: 0.85;
            margin-left: 0.25rem;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }
        .topbar-link {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
            text-decoration: none;
        }
        .topbar-link:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        /* Page Content */
        .attendance-page {
            max-width: 900px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }

        /* Alert styles */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            border: 1px solid transparent;
        }
        .alert-success { background: var(--success-bg); color: #065f46; border-color: #a7f3d0; }
        .alert-error { background: var(--danger-bg); color: #991b1b; border-color: #fecaca; }

        /* Buttons */
        .btn {
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            line-height: 1;
        }
        .btn-sm { padding: 0.45rem 0.85rem; font-size: 0.8rem; }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: #fff;
        }
        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: #fff;
        }
        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.4s ease forwards; }

        @media (max-width: 640px) {
            .attendance-page { padding: 1.5rem 1rem 3rem; }
            .attendance-topbar { padding: 0 1rem; }
            .topbar-brand-text { font-size: 1.1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="attendance-topbar">
        <div class="topbar-brand">
            <div class="topbar-brand-icon">☕</div>
            <div class="topbar-brand-text">Keshir<span>Absensi</span></div>
        </div>
        <div class="topbar-right">
            <a href="{{ route('login') }}" class="topbar-link">
                🔑 Login Dashboard
            </a>
        </div>
    </nav>

    <div class="attendance-page animate-in">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
