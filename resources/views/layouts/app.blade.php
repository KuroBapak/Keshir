<!DOCTYPE html>
<html lang="id" data-lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Keshir POS')</title>
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
            --info: #0ea5e9;
            --info-bg: #e0f2fe;
            --sidebar-w: 260px;
            --nav-h: 72px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
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

        /* Enhanced Top Navigation with Gradient */
        .top-nav {
            background: linear-gradient(135deg, #1e3a8a 0%, var(--primary) 35%, var(--accent) 100%);
            color: #fff;
            padding: 0 1.5rem;
            height: var(--nav-h);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            box-shadow: 0 4px 30px rgba(37, 99, 235, 0.35);
            overflow: hidden;
        }
        /* Animated gradient overlay */
        .top-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 200%;
            height: 100%;
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
        /* Decorative circles */
        .top-nav::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .top-nav .brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            position: relative;
            z-index: 1;
        }
        .top-nav .brand-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.1) 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .top-nav .brand-text {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .top-nav .brand-text span {
            font-weight: 400;
            opacity: 0.85;
            margin-left: 0.25rem;
        }
        
        .top-nav .right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }
        
        /* Language Switcher */
        .lang-switcher {
            position: relative;
            z-index: 9999;
        }
        .lang-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.85rem;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.85rem;
            font-weight: 600;
            color: #fff;
            backdrop-filter: blur(10px);
        }
        .lang-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
        }
        .lang-btn .flag {
            font-size: 1.1rem;
        }
        .lang-dropdown {
            position: fixed;
            top: 65px;
            right: 200px;
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            min-width: 160px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 9999;
            overflow: hidden;
        }
        .lang-switcher:hover .lang-dropdown,
        .lang-switcher.active .lang-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .lang-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background 0.15s ease;
            color: var(--text);
            font-size: 0.875rem;
            font-weight: 500;
        }
        .lang-option:hover {
            background: var(--primary-50);
        }
        .lang-option.active {
            background: var(--primary-100);
            color: var(--primary-dark);
        }
        .lang-option .flag { font-size: 1.25rem; }
        .lang-option .check { 
            margin-left: auto; 
            color: var(--primary);
            opacity: 0;
        }
        .lang-option.active .check { opacity: 1; }
        
        .top-nav .divider {
            width: 1px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            margin: 0 0.25rem;
        }
        
        .top-nav .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem 0.5rem 0.5rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }
        .top-nav .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.8) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .top-nav .user-details {
            font-size: 0.85rem;
            line-height: 1.3;
        }
        .top-nav .user-name { font-weight: 600; }
        .top-nav .user-role { opacity: 0.85; font-size: 0.75rem; }
        
        .top-nav .logout-btn {
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
        }
        .top-nav .logout-btn:hover {
            background: rgba(239, 68, 68, 0.9);
            border-color: rgba(239, 68, 68, 1);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }

        /* Layout */
        .layout {
            display: flex;
            margin-top: var(--nav-h);
            min-height: calc(100vh - var(--nav-h));
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--card);
            border-right: 1px solid var(--border);
            position: fixed;
            top: var(--nav-h);
            bottom: 0;
            overflow-y: auto;
            padding: 1.5rem 0;
            transition: all 0.3s ease;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        
        .sidebar .section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: 0.1em;
            margin-top: 1rem;
        }
        .sidebar .section-title:first-child { margin-top: 0; }
        
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            margin: 0.125rem 0;
        }
        .sidebar a .icon {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        .sidebar a:hover {
            background: var(--primary-50);
            color: var(--primary);
        }
        .sidebar a.active {
            background: linear-gradient(90deg, var(--primary-100) 0%, var(--primary-50) 100%);
            color: var(--primary);
            border-left-color: var(--primary);
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            padding: 2rem;
            max-width: 100%;
            min-width: 0;
        }

        /* Cards */
        .card {
            background: var(--card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-light);
            transition: box-shadow 0.2s ease;
        }
        .card:hover { box-shadow: var(--shadow-md); }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
        }
        .page-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .page-header .subtitle {
            font-size: 0.875rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }

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
        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: #fff;
        }
        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: #fff;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }
        .btn-outline:hover {
            background: var(--bg);
            border-color: var(--primary);
            color: var(--primary);
        }
        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
        }
        .btn-ghost:hover { background: var(--bg); color: var(--primary); }
        .btn-sm { padding: 0.45rem 0.85rem; font-size: 0.8rem; }
        .btn-xs { padding: 0.35rem 0.65rem; font-size: 0.75rem; border-radius: 0.5rem; }
        .btn-icon { width: 36px; height: 36px; padding: 0; border-radius: 0.5rem; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; }
        th, td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.875rem;
        }
        th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: 0.05em;
            background: var(--bg);
        }
        tbody tr { transition: background 0.15s ease; }
        tbody tr:hover { background: var(--primary-50); }
        tbody tr:last-child td { border-bottom: none; }

        /* Forms */
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text);
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-family: inherit;
            transition: all 0.2s ease;
            background: var(--card);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-100);
        }
        .form-control::placeholder { color: var(--muted); }
        select.form-control { appearance: auto; cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .form-inline { display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap; }
        .form-inline .form-group { margin-bottom: 0; }
        .form-hint { font-size: 0.8rem; color: var(--muted); margin-top: 0.35rem; }

        /* Alerts */
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
        .alert-icon { font-size: 1.1rem; flex-shrink: 0; }
        .alert-success { background: var(--success-bg); color: #065f46; border-color: #a7f3d0; }
        .alert-error { background: var(--danger-bg); color: #991b1b; border-color: #fecaca; }
        .alert-warning { background: var(--warning-bg); color: #92400e; border-color: #fde68a; }
        .alert-info { background: var(--info-bg); color: #0c4a6e; border-color: #7dd3fc; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }
        .badge-success { background: var(--success-bg); color: #065f46; }
        .badge-warning { background: var(--warning-bg); color: #92400e; }
        .badge-danger { background: var(--danger-bg); color: #991b1b; }
        .badge-info { background: var(--info-bg); color: #0c4a6e; }
        .badge-primary { background: var(--primary-100); color: var(--primary-dark); }
        .badge-gray { background: var(--bg-dark); color: var(--muted); }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--muted);
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .empty-state h3 { font-size: 1.1rem; color: var(--text); margin-bottom: 0.5rem; }
        .empty-state p { font-size: 0.875rem; }

        /* Grid & Utilities */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
        @media (max-width: 1024px) {
            .grid-3, .grid-4 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: var(--muted); }
        .text-primary { color: var(--primary); }
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }

        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .mr-1 { margin-right: 0.5rem; }
        .ml-1 { margin-left: 0.5rem; }

        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-1 { gap: 0.5rem; }
        .gap-2 { gap: 1rem; }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.3s ease; }

        /* Pagination - Laravel Default */
        .pagination { 
            display: flex; 
            gap: 0.25rem; 
            justify-content: center; 
            align-items: center;
            margin-top: 1.5rem; 
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 0.5rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            text-decoration: none;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            transition: all 0.2s ease;
            background: var(--card);
        }
        .pagination a:hover { background: var(--primary-50); border-color: var(--primary); color: var(--primary); }
        .pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }
        .pagination .disabled span { opacity: 0.5; cursor: not-allowed; }
        
        /* Tailwind Pagination Support - Complete Override */
        nav[role="navigation"] {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        nav[role="navigation"] > div {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        nav[role="navigation"] p.text-sm,
        nav[role="navigation"] .text-sm {
            color: var(--muted);
            font-size: 0.85rem;
        }
        /* Active page number */
        nav[role="navigation"] span[aria-current="page"] span,
        nav[role="navigation"] .relative.z-10,
        nav[role="navigation"] span[aria-current="page"] {
            background: var(--primary) !important;
            color: #fff !important;
            border-color: var(--primary) !important;
            border: 1px solid var(--primary) !important;
            padding: 0.5rem 0.85rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            min-width: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        /* Prev/Next buttons */
        nav[role="navigation"] a[rel="prev"],
        nav[role="navigation"] a[rel="next"],
        nav[role="navigation"] span[aria-disabled="true"] {
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.85rem !important;
            text-decoration: none !important;
            border: 1px solid var(--border) !important;
            background: var(--card) !important;
            color: var(--text) !important;
            transition: all 0.2s ease;
        }
        nav[role="navigation"] a[rel="prev"]:hover,
        nav[role="navigation"] a[rel="next"]:hover {
            background: var(--primary-50) !important;
            border-color: var(--primary) !important;
            color: var(--primary) !important;
        }
        nav[role="navigation"] span[aria-disabled="true"] {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }
        /* ALL page number links - both active and inactive */
        nav[role="navigation"] span.relative,
        nav[role="navigation"] a.relative {
            padding: 0.5rem 0.85rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.85rem !important;
            text-decoration: none !important;
            border: 1px solid var(--border) !important;
            background: var(--card) !important;
            color: var(--text) !important;
            transition: all 0.2s ease;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 40px !important;
            box-shadow: none !important;
        }
        nav[role="navigation"] a.relative:hover {
            background: var(--primary-50) !important;
            border-color: var(--primary) !important;
            color: var(--primary) !important;
        }
        /* Override Tailwind ring and focus styles */
        nav[role="navigation"] .ring-1,
        nav[role="navigation"] [class*="ring-"],
        nav[role="navigation"] .focus\\:ring,
        nav[role="navigation"] [class*="focus:"] {
            box-shadow: none !important;
            --tw-ring-shadow: none !important;
        }
        /* Reset all inline styles from Tailwind */
        nav[role="navigation"] span[style],
        nav[role="navigation"] a[style] {
            all: unset;
        }
        nav[role="navigation"] .hidden.sm\\:flex-1 {
            display: none !important;
        }
        nav[role="navigation"] > div:first-child {
            display: none;
        }
        nav[role="navigation"] > div:last-child {
            width: 100%;
            justify-content: center;
        }
        nav[role="navigation"] .flex.justify-between {
            justify-content: center !important;
            gap: 0.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1rem; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .top-nav { padding: 0.75rem 1rem; }
            .top-nav .brand-icon { width: 38px; height: 38px; font-size: 1.2rem; }
            .top-nav .brand-text { font-size: 1.1rem; }
            .top-nav .user-details { display: none; }
            .top-nav .user-info { padding: 0.35rem; }
            .top-nav .divider { display: none; }
            .lang-btn span:not(.flag) { display: none; }
            .lang-btn { padding: 0.5rem 0.65rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="top-nav">
        <div class="brand">
            <div class="brand-icon">☕</div>
            <div class="brand-text">Keshir<span>POS</span></div>
        </div>
        <div class="right">
            <!-- Language Switcher -->
            <div class="lang-switcher" id="langSwitcher">
                <button class="lang-btn" onclick="toggleLangDropdown()">
                    <span class="flag" id="currentFlag">🇮🇩</span>
                    <span id="currentLang">ID</span>
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="lang-dropdown">
                    <div class="lang-option" data-lang="id" onclick="setLanguage('id')">
                        <span class="flag">🇮🇩</span>
                        <span>Indonesia</span>
                        <span class="check">✓</span>
                    </div>
                    <div class="lang-option" data-lang="en" onclick="setLanguage('en')">
                        <span class="flag">🇺🇸</span>
                        <span>English</span>
                        <span class="check">✓</span>
                    </div>
                </div>
            </div>
            
            <div class="divider"></div>
            
            @auth
                <div class="user-info">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">{{ ucfirst(Auth::user()->role->name) }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        <span data-i18n="logout">Logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="logout-btn" style="text-decoration:none;">
                    <span data-i18n="login">Login</span>
                </a>
            @endauth
        </div>
    </nav>

    <div class="layout">
        <aside class="sidebar">
            @auth
            @php $role = auth()->user()->role->name; @endphp

            @if(in_array($role, ['owner', 'manager']))
            <div class="section-title" data-i18n="menu_main">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> <span data-i18n="dashboard">Dashboard</span>
            </a>

            <div class="section-title" data-i18n="master_data">Master Data</div>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <span class="icon">📂</span> <span data-i18n="categories">Kategori</span>
            </a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <span class="icon">🍽️</span> <span data-i18n="products">Produk</span>
            </a>
            <a href="{{ route('ingredients.index') }}" class="{{ request()->routeIs('ingredients.*') ? 'active' : '' }}">
                <span class="icon">🧪</span> <span data-i18n="ingredients">Bahan Baku</span>
            </a>
            <a href="{{ route('recipes.index') }}" class="{{ request()->routeIs('recipes.*') || request()->routeIs('products.recipe.*') ? 'active' : '' }}">
                <span class="icon">📋</span> <span data-i18n="recipes">Resep</span>
            </a>

            <div class="section-title" data-i18n="operations">Operasional</div>
            <a href="{{ route('tables.index') }}" class="{{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <span class="icon">🪑</span> <span data-i18n="tables">Meja</span>
            </a>
            <a href="{{ route('discounts.index') }}" class="{{ request()->routeIs('discounts.*') ? 'active' : '' }}">
                <span class="icon">🏷️</span> <span data-i18n="discounts">Diskon</span>
            </a>
            <a href="{{ route('cash-drawer.index') }}" class="{{ request()->routeIs('cash-drawer.*') ? 'active' : '' }}">
                <span class="icon">💰</span> <span data-i18n="cash_drawer">Kas Laci</span>
            </a>
            <a href="{{ route('refunds.index') }}" class="{{ request()->routeIs('refunds.*') ? 'active' : '' }}">
                <span class="icon">🔄</span> <span data-i18n="refunds">Refund</span>
            </a>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <span class="icon">⚙️</span> <span data-i18n="settings">Pengaturan</span>
            </a>
            <div class="section-title" data-i18n="reports">Laporan</div>
            <a href="{{ route('reports.daily') }}" class="{{ request()->routeIs('reports.daily') ? 'active' : '' }}">
                <span class="icon">📊</span> <span data-i18n="daily_report">Laporan Harian</span>
            </a>
            <a href="{{ route('reports.best-selling') }}" class="{{ request()->routeIs('reports.best-selling') ? 'active' : '' }}">
                <span class="icon">🏆</span> <span data-i18n="best_selling">Produk Terlaris</span>
            </a>

            <div class="section-title" data-i18n="point_of_sale">Point of Sale</div>
            <a href="{{ route('pos.index') }}">
                <span class="icon">🧾</span> <span data-i18n="pos_cashier">Kasir POS</span>
            </a>
            <a href="{{ route('kitchen.index') }}">
                <span class="icon">👨‍🍳</span> <span data-i18n="kitchen">Dapur</span>
            </a>
            @endif

            @if($role === 'cashier')
            <div class="section-title" data-i18n="cashier">Kasir</div>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') && !request()->routeIs('pos.bookings') ? 'active' : '' }}">
                <span class="icon">🧾</span> <span data-i18n="pos_cashier">POS Kasir</span>
            </a>
            <a href="{{ route('pos.bookings') }}" class="{{ request()->routeIs('pos.bookings') ? 'active' : '' }}">
                <span class="icon">📅</span> <span data-i18n="reservations">Reservasi / Booking</span>
            </a>
            <a href="{{ route('cash-drawer.index') }}" class="{{ request()->routeIs('cash-drawer.*') && !request()->routeIs('cash-drawer.shift-sales') ? 'active' : '' }}">
                <span class="icon">💰</span> <span data-i18n="cash_drawer">Kas Laci</span>
            </a>
            <a href="{{ route('cash-drawer.shift-sales') }}" class="{{ request()->routeIs('cash-drawer.shift-sales') ? 'active' : '' }}">
                <span class="icon">📊</span> <span data-i18n="shift_sales">Penjualan Shift</span>
            </a>
            <a href="{{ route('refunds.index') }}" class="{{ request()->routeIs('refunds.*') ? 'active' : '' }}">
                <span class="icon">🔄</span> <span data-i18n="refunds">Refund</span>
            </a>
            @endif
            @endauth
        </aside>

        <div class="main-content animate-fade-in">
            @if(session('success'))
                <div class="alert alert-success">
                    <span class="alert-icon">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <span class="alert-icon">❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">
                    <span class="alert-icon">⚠️</span>
                    <span>{!! session('warning') !!}</span>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">
                    <span class="alert-icon">ℹ️</span>
                    <span>{{ session('info') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <span class="alert-icon">❌</span>
                    <div>
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <!-- Language Translations -->
    <script>
        const translations = {
            id: {
                // Navigation
                menu_main: 'Menu Utama',
                dashboard: 'Dashboard',
                master_data: 'Master Data',
                categories: 'Kategori',
                products: 'Produk',
                ingredients: 'Bahan Baku',
                recipes: 'Resep',
                operations: 'Operasional',
                tables: 'Meja',
                discounts: 'Diskon',
                cash_drawer: 'Kas Laci',
                refunds: 'Refund',
                settings: 'Pengaturan',
                attendance: 'Absensi',
                reports: 'Laporan',
                daily_report: 'Laporan Harian',
                best_selling: 'Produk Terlaris',
                point_of_sale: 'Point of Sale',
                pos_cashier: 'Kasir POS',
                kitchen: 'Dapur',
                cashier: 'Kasir',
                reservations: 'Reservasi / Booking',
                shift_sales: 'Penjualan Shift',
                logout: 'Logout',
                login: 'Login',
            },
            en: {
                // Navigation
                menu_main: 'Main Menu',
                dashboard: 'Dashboard',
                master_data: 'Master Data',
                categories: 'Categories',
                products: 'Products',
                ingredients: 'Ingredients',
                recipes: 'Recipes',
                operations: 'Operations',
                tables: 'Tables',
                discounts: 'Discounts',
                cash_drawer: 'Cash Drawer',
                refunds: 'Refunds',
                settings: 'Settings',
                attendance: 'Attendance',
                reports: 'Reports',
                daily_report: 'Daily Report',
                best_selling: 'Best Selling',
                point_of_sale: 'Point of Sale',
                pos_cashier: 'POS Cashier',
                kitchen: 'Kitchen',
                cashier: 'Cashier',
                reservations: 'Reservations / Booking',
                shift_sales: 'Shift Sales',
                logout: 'Logout',
                login: 'Login',
            }
        };

        function toggleLangDropdown(event) {
            event.stopPropagation();
            const switcher = document.getElementById('langSwitcher');
            const dropdown = switcher.querySelector('.lang-dropdown');
            const btn = switcher.querySelector('.lang-btn');
            
            switcher.classList.toggle('active');
            
            // Position dropdown dynamically
            if (switcher.classList.contains('active')) {
                const rect = btn.getBoundingClientRect();
                dropdown.style.top = (rect.bottom + 8) + 'px';
                dropdown.style.right = (window.innerWidth - rect.right) + 'px';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const switcher = document.getElementById('langSwitcher');
            if (switcher && !switcher.contains(e.target)) {
                switcher.classList.remove('active');
            }
        });

        function setLanguage(lang) {
            localStorage.setItem('keshir_lang', lang);
            document.documentElement.setAttribute('data-lang', lang);
            applyTranslations(lang);
            updateLangUI(lang);
            document.getElementById('langSwitcher').classList.remove('active');
        }

        function applyTranslations(lang) {
            const t = translations[lang] || translations.id;
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (t[key]) {
                    el.textContent = t[key];
                }
            });
        }

        function updateLangUI(lang) {
            const flag = lang === 'en' ? '🇺🇸' : '🇮🇩';
            const code = lang === 'en' ? 'EN' : 'ID';
            document.getElementById('currentFlag').textContent = flag;
            document.getElementById('currentLang').textContent = code;
            
            // Update active state
            document.querySelectorAll('.lang-option').forEach(opt => {
                opt.classList.toggle('active', opt.dataset.lang === lang);
            });
        }

        // Initialize language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('keshir_lang') || 'id';
            document.documentElement.setAttribute('data-lang', savedLang);
            applyTranslations(savedLang);
            updateLangUI(savedLang);
        });
    </script>

    @stack('scripts')
</body>
</html>
