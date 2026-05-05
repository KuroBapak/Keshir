<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kios Keshir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Load Midtrans Snap JS -->
    @php
        $isProd = env('MIDTRANS_IS_PRODUCTION', config('midtrans.is_production', false));
        $snapUrl = $isProd ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = env('MIDTRANS_CLIENT_KEY', config('midtrans.client_key'));
    @endphp
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-bg: #eef2ff;
            --bg-color: #f3f4f6;
            --white: #ffffff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --success: #10b981;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Top Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            z-index: 10;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .brand-logo {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .date-time {
            font-size: 0.9rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-bg);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.2s;
        }

        .icon-btn:hover {
            background: var(--primary);
            color: var(--white);
        }

        /* Main Layout */
        .main-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Left Side (Menu Content) */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            overflow-y: auto;
        }

        /* Search Bar */
        .search-bar {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem; /* increased */
            flex-shrink: 0; /* Important to prevent squashing */
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
            background: transparent;
        }

        /* Categories */
        .category-scroll-wrapper {
            position: relative;
            margin-bottom: 2rem;
            flex-shrink: 0;
        }
        .category-row {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
            gap: 0.75rem;
            padding-bottom: 0.5rem;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .category-row::-webkit-scrollbar {
            display: none;
        }


        .category-card {
            background: var(--white);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.5rem 1.5rem 0.5rem 0.5rem;
            flex: 0 0 calc(33.333% - 0.5rem);
            scroll-snap-align: start;
            min-width: 200px;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            height: 64px;
        }

        .category-card:hover, .category-card.active {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 4px 15px rgba(37,99,235,0.15);
        }

        .cat-img-box {
            width: 56px;
            height: 56px;
            background: var(--primary-bg); /* subtle gray/blue circle */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid var(--border-color);
        }

        .cat-img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .cat-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }

        .cat-text .cat-name {
            font-weight: 900;
            font-size: 1.05rem;
            letter-spacing: -0.5px;
            color: var(--text-dark);
            margin-bottom: 0.1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cat-text .cat-items {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .category-card .cat-name {
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: -0.5px;
        }

        .category-card .cat-items {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1rem;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }

        .product-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .product-image {
            width: 100%;
            height: 120px;
            background: var(--bg-color);
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-name {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--text-dark);
        }

        .product-info {
            min-width: 0;
        }

        .product-price {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .product-variants {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-top: 0.35rem;
        }

        .variant-tag {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 50px;
            background: var(--primary-bg);
            color: var(--primary);
            border: 1px solid rgba(37, 99, 235, 0.15);
            white-space: nowrap;
        }

        .add-btn {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--text-dark);
            background: var(--white);
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .product-card:hover .add-btn {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        /* Right Side (Cart / Sidebar) */
        .sidebar {
            width: 400px;
            background: var(--white);
            border-left: 2px solid var(--primary);
            display: flex;
            flex-direction: column;
            margin: 1.5rem 1.5rem 1.5rem 0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .sidebar form#checkoutForm {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px dashed var(--border-color);
            flex-shrink: 0;
        }

        .sidebar-header h2 {
            font-weight: 900;
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .order-toggle {
            display: flex;
            background: var(--bg-color);
            border-radius: 50px;
            padding: 0.25rem;
            margin: 1rem 1.5rem;
            flex-shrink: 0;
        }

        .order-type-btn {
            flex: 1;
            padding: 0.5rem;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.85rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
        }

        .order-type-btn.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        }

        .customer-form {
            padding: 0 1.5rem 1rem;
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 1rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            font-size: 0.85rem;
            outline: none;
            background: var(--white);
        }

        /* Form Group Full Width for Phone */
        .form-group-full {
            grid-column: 1 / -1;
            margin-bottom: 0.5rem;
        }
        .form-group-full input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            font-size: 0.85rem;
            outline: none;
            background: var(--white);
        }

        .cart-title {
            padding: 0 1.5rem;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .sidebar-scrollable {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        .cart-items {
            padding: 0 1.5rem;
        }

        .cart-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .cart-item-img {
            width: 50px;
            height: 50px;
            background: var(--bg-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .cart-item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .cart-item-price {
            font-size: 0.8rem;
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .cart-item-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            background: var(--bg-color);
            border-radius: 50px;
            padding: 0.25rem;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border: none;
            background: var(--white);
            border-radius: 50%;
            font-weight: 700;
            cursor: pointer;
            color: var(--text-dark);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .qty-input {
            width: 30px;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .sidebar-footer {
            padding: 1.5rem;
            background: var(--white);
            border-top: 1px dashed var(--border-color);
            flex-shrink: 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .summary-row.total {
            font-size: 1.1rem;
            font-weight: 900;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border-color);
        }

        .place-order-btn {
            width: 100%;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: 0.2s;
            box-shadow: 0 4px 15px rgba(37,99,235,0.3);
        }

        .place-order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.4);
        }
        
        .place-order-btn:disabled {
            background: var(--text-muted);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* Product Modal */
        .product-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
        }
        .product-modal.show {
            display: flex;
        }
        .pm-content {
            background: var(--white);
            width: 450px;
            max-width: 90%;
            border-radius: 16px;
            padding: 1.25rem;
            max-height: 90vh;
            overflow-y: auto;
        }
        .pm-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .pm-header h3 { font-size: 1.25rem; font-weight: 800; }
        .pm-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); }
        .pm-group { margin-bottom: 1rem; }
        .pm-group > label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; }
        .radio-grid { display: grid; gap: 0.5rem; }
        .radio-card {
            border: 1px solid var(--border-color);
            padding: 0.75rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .radio-card .rc-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .radio-card .rc-price {
            color: var(--primary);
            font-weight: 700;
            font-size: 0.85rem;
            white-space: nowrap;
        }
        .radio-card input { margin: 0; }
        .pm-notes {
            width: 100%;
            min-height: 74px;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            resize: vertical;
        }
        .pm-submit { width: 100%; padding: 1rem; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 800; cursor: pointer; margin-top: 1rem; }

        .pm-hero {
            width: 100%;
            height: 220px;
            border-radius: 14px;
            background: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 0.9rem;
        }

        .pm-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pm-hero-fallback {
            font-size: 3rem;
            opacity: 0.2;
        }

        .pm-desc {
            font-size: 0.92rem;
            color: #374151;
            line-height: 1.45;
            margin-bottom: 1rem;
        }

        .pm-divider {
            border-top: 1px solid var(--border-color);
            margin: 0.9rem 0;
        }

        .pm-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.25rem;
            position: sticky;
            bottom: -1.25rem;
            background: var(--white);
            padding-top: 0.7rem;
        }

        .pm-qty-control {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--bg-color);
            border-radius: 999px;
            padding: 0.25rem;
        }

        .pm-qty-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: var(--white);
            cursor: pointer;
            font-weight: 700;
        }

        .pm-qty-input {
            width: 34px;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.95rem;
        }

        /* Payment Choice Modal */
        .payment-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .payment-modal.show {
            display: flex;
        }

        .payment-content {
            display: flex;
            gap: 1.5rem;
            position: relative;
        }

        .payment-btn {
            background: var(--white);
            width: 200px;
            height: 250px;
            border-radius: 16px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--text-dark);
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }

        .payment-btn:hover {
            transform: scale(1.05);
            color: var(--primary);
        }
        
        .d-none { display: none !important; }

        .mobile-cart-bar,
        .mobile-cart-backdrop,
        .sidebar-mobile-close {
            display: none;
        }

        /* Chatbot FAB */
        .chatbot-fab {
            position: fixed;
            right: 1.5rem;
            bottom: 1.5rem;
            z-index: 95;
            border: none;
            border-radius: 999px;
            padding: 0.85rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            background: linear-gradient(135deg, #1577ff 0%, #0f63e6 100%);
            color: var(--white);
            box-shadow: 0 14px 30px rgba(21, 119, 255, 0.28);
            cursor: pointer;
            font-weight: 800;
            transition: all 0.3s cubic-bezier(.4,0,.2,1);
        }
        .chatbot-fab:hover {
            transform: scale(1.06);
            box-shadow: 0 18px 38px rgba(21, 119, 255, 0.35);
        }
        .chatbot-fab.hidden { display: none; }

        .chatbot-fab-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.18);
            font-size: 1.1rem;
        }

        .chatbot-fab-label {
            display: inline-block;
            font-size: 0.92rem;
            letter-spacing: 0.2px;
        }

        /* ===== Chat Panel ===== */
        .chat-panel {
            position: fixed;
            right: 1.5rem;
            bottom: 1.5rem;
            width: 400px;
            max-width: calc(100vw - 2rem);
            height: 560px;
            max-height: calc(100vh - 3rem);
            z-index: 200;
            border-radius: 20px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(18px) saturate(1.6);
            -webkit-backdrop-filter: blur(18px) saturate(1.6);
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow:
                0 24px 60px rgba(15, 23, 42, 0.18),
                0 0 0 1px rgba(21, 119, 255, 0.08);
            animation: chatSlideUp 0.35s cubic-bezier(.4,0,.2,1);
        }
        .chat-panel.open { display: flex; }

        @keyframes chatSlideUp {
            from { opacity: 0; transform: translateY(24px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chat-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            background: linear-gradient(135deg, #1577ff 0%, #0f63e6 100%);
            color: white;
            flex-shrink: 0;
        }
        .chat-header-avatar {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
        }
        .chat-header-info { flex: 1; }
        .chat-header-info h4 { font-size: 0.95rem; font-weight: 800; margin: 0; }
        .chat-header-info span { font-size: 0.72rem; opacity: 0.8; font-weight: 500; }
        .chat-close-btn {
            background: rgba(255,255,255,0.18);
            border: none; color: white;
            width: 32px; height: 32px; border-radius: 50%;
            font-size: 1.15rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .chat-close-btn:hover { background: rgba(255,255,255,0.3); }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            scroll-behavior: smooth;
        }

        .chat-bubble {
            max-width: 85%;
            padding: 0.7rem 0.9rem;
            border-radius: 16px;
            font-size: 0.88rem;
            line-height: 1.55;
            word-break: break-word;
        }
        .chat-bubble.bot {
            align-self: flex-start;
            background: #f0f4ff;
            color: #1e293b;
            border-bottom-left-radius: 4px;
        }
        .chat-bubble.user {
            align-self: flex-end;
            background: linear-gradient(135deg, #1577ff 0%, #0f63e6 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .chat-bubble.bot strong { color: var(--primary); }
        .chat-bubble.bot img {
            max-width: 100%;
            border-radius: 10px;
            margin: 0.5rem 0;
        }

        /* Typing indicator */
        .typing-indicator {
            align-self: flex-start;
            display: flex;
            gap: 5px;
            padding: 0.8rem 1rem;
            background: #f0f4ff;
            border-radius: 16px 16px 16px 4px;
        }
        .typing-indicator span {
            width: 8px; height: 8px;
            background: #94a3b8;
            border-radius: 50%;
            animation: typingBounce 1.2s infinite;
        }
        .typing-indicator span:nth-child(2) { animation-delay: 0.15s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.3s; }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        .chat-input-area {
            display: flex;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-top: 1px solid rgba(226,232,240,0.7);
            background: rgba(255,255,255,0.85);
            flex-shrink: 0;
        }
        .chat-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.65rem 0.85rem;
            font-size: 0.88rem;
            font-family: inherit;
            outline: none;
            background: white;
            transition: border 0.2s;
        }
        .chat-input:focus { border-color: var(--primary); }
        .chat-send-btn {
            width: 40px; height: 40px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #1577ff 0%, #0f63e6 100%);
            color: white; font-size: 1.1rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
            flex-shrink: 0;
        }
        .chat-send-btn:hover { transform: scale(1.08); }
        .chat-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* Quick Reply Buttons */
        .chat-quick-replies {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            padding: 0.5rem 1rem 0;
        }
        .quick-reply-btn {
            padding: 0.45rem 0.85rem;
            border-radius: 50px;
            border: 1px solid #cbd5e1;
            background: white;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            color: #334155;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .quick-reply-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #eef2ff;
        }

        /* Reset / Clear Chat */
        .chat-reset-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.7);
            font-size: 0.92rem;
            cursor: pointer;
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .chat-reset-btn:hover { color: white; background: rgba(255,255,255,0.18); }

        /* Chat action buttons (add to cart from chat) */
        .chat-action-btn {
            display: inline-block;
            margin-top: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            border: 1px solid var(--primary);
            background: #eef2ff;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chat-action-btn:hover {
            background: var(--primary);
            color: white;
        }

        @media (max-width: 992px) {
            body {
                overflow: hidden;
                background:
                    radial-gradient(circle at top, rgba(21, 119, 255, 0.14), transparent 30%),
                    linear-gradient(180deg, #eef5ff 0%, #f7f8fc 20%, #f3f4f6 100%);
            }

            .top-header {
                padding: 1rem 1rem 0.95rem;
                background: linear-gradient(135deg, #1577ff 0%, #0f63e6 100%);
                color: var(--white);
                box-shadow: none;
                border-bottom-left-radius: 18px;
                border-bottom-right-radius: 18px;
                position: sticky;
                top: 0;
                z-index: 20;
            }

            .brand-logo {
                color: var(--white);
                font-size: 1.55rem;
                letter-spacing: 0.5px;
            }

            .date-time {
                display: none;
            }

            .header-right {
                display: none;
            }

            .main-container {
                display: block;
                overflow: hidden;
            }

            .content-area {
                padding: 0.8rem 0.8rem 0.9rem;
                height: calc(100vh - 72px);
                overflow-y: auto;
                padding-bottom: 8rem;
            }

            .search-bar {
                display: flex;
                margin-bottom: 0.85rem;
                border: none;
                background: rgba(255,255,255,0.92);
                border-radius: 16px;
                padding: 0.85rem 1rem;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            }

            .category-row {
                gap: 0.45rem;
                margin-bottom: 0.95rem;
                padding-bottom: 0;
            }

            .category-card {
                height: 50px;
                padding: 0.35rem 0.5rem;
                justify-content: center;
                border-radius: 16px;
                border-width: 1px;
                box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
                background: rgba(255,255,255,0.95);
                flex: 0 0 auto;
                min-width: 120px;
            }

            .cat-img-box {
                width: 58px;
                height: 58px;
            }

            .cat-img {
                width: 40px;
                height: 40px;
            }

            .category-card .cat-img-box,
            .category-card .cat-items {
                display: none;
            }

            .cat-text {
                display: block;
                width: 100%;
                text-align: center;
            }

            .category-card .cat-name {
                font-size: 0.95rem;
                font-weight: 800;
                margin: 0;
                letter-spacing: 0;
            }

            .category-card.active {
                background: linear-gradient(135deg, #1577ff 0%, #0f63e6 100%);
                border-color: transparent;
                box-shadow: 0 10px 24px rgba(21, 119, 255, 0.22);
            }

            .category-card.active .cat-name {
                color: var(--white);
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 0.7rem;
            }

            .product-card {
                border-radius: 20px;
                border: 1px solid rgba(226, 232, 240, 0.9);
                min-height: 120px;
                padding: 0.7rem 4.4rem 0.7rem 0.7rem;
                display: flex;
                align-items: center;
                gap: 0.8rem;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
                background: rgba(255,255,255,0.96);
            }

            .product-card:hover {
                transform: none;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
                border-color: rgba(226, 232, 240, 0.9);
            }

            .product-image {
                width: 82px;
                height: 82px;
                margin-bottom: 0;
                border-radius: 18px;
                flex-shrink: 0;
                background: linear-gradient(180deg, #f7f7fb 0%, #edf2ff 100%);
            }

            .product-name {
                margin-bottom: 0.15rem;
                font-size: 1.18rem;
                line-height: 1.1;
                font-weight: 800;
            }

            .product-info {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
                min-width: 0;
            }

            .product-price {
                font-size: 0.9rem;
                margin-bottom: 0.15rem;
                color: #6b7280;
            }

            .product-card .add-btn {
                top: 0;
                right: 0;
                bottom: 0;
                width: 46px;
                height: 100%;
                border-radius: 0 20px 20px 0;
                border: none;
                background: linear-gradient(180deg, #1577ff 0%, #0f63e6 100%);
                color: var(--white);
                font-size: 1.75rem;
                font-weight: 700;
                box-shadow: inset 1px 0 0 rgba(255,255,255,0.12);
            }

            .mobile-cart-bar {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 60;
                background: var(--white);
                border-top: 1px solid rgba(229,231,235,0.9);
                box-shadow: 0 -10px 30px rgba(15, 23, 42, 0.08);
                padding: 0.75rem 1rem calc(0.75rem + env(safe-area-inset-bottom));
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                border-top-left-radius: 20px;
                border-top-right-radius: 20px;
            }

            .mobile-cart-total {
                font-size: 0.95rem;
                font-weight: 700;
                display: flex;
                flex-direction: column;
                line-height: 1.25;
            }

            .mobile-cart-total strong {
                font-size: 1.35rem;
                font-weight: 900;
            }

            .mobile-cart-open-btn {
                border: none;
                width: 42px;
                height: 42px;
                border-radius: 50%;
                background: linear-gradient(135deg, #1577ff 0%, #0f63e6 100%);
                color: var(--white);
                font-size: 1.45rem;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 8px 18px rgba(21, 119, 255, 0.2);
            }

            .chatbot-fab {
                right: 0.9rem;
                bottom: calc(5.4rem + env(safe-area-inset-bottom));
                padding: 0.72rem 0.9rem;
            }

            .chatbot-fab-label {
                display: inline-block;
                font-size: 0.85rem;
            }

            .chatbot-fab-icon {
                width: 34px;
                height: 34px;
                font-size: 1rem;
            }

            .chat-panel {
                right: 0;
                bottom: 0;
                width: 100vw;
                max-width: 100vw;
                height: 100vh;
                max-height: 100vh;
                border-radius: 0;
            }

            .mobile-cart-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.38);
                backdrop-filter: blur(3px);
                z-index: 70;
            }

            .mobile-cart-backdrop.show {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: 0;
                right: 0;
                height: 100vh;
                width: min(92vw, 430px);
                margin: 0;
                border-radius: 16px 0 0 16px;
                border-left: 1px solid rgba(229,231,235,0.9);
                z-index: 80;
                transform: translateX(100%);
                transition: transform 0.25s ease;
                overflow-y: auto;
                box-shadow: -16px 0 40px rgba(15, 23, 42, 0.12);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-mobile-close {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                border-radius: 50%;
                border: 1px solid var(--border-color);
                background: var(--white);
                font-size: 1.2rem;
                cursor: pointer;
                margin: 0.85rem 0.85rem 0 auto;
                box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
            }
        }

        @media (max-width: 460px) {
            .brand-logo {
                font-size: 1.35rem;
            }

            .product-name {
                font-size: 1.05rem;
            }

            .mobile-cart-total strong {
                font-size: 1.2rem;
            }

            .chatbot-fab {
                right: 0.85rem;
            }
            .chat-panel {
                border-radius: 0;
            }

            .product-modal {
                align-items: flex-end;
            }

            .pm-content {
                width: 100%;
                max-width: 100%;
                max-height: calc(100vh - 42px);
                border-radius: 16px 16px 0 0;
                padding: 1rem;
            }

            .pm-hero {
                height: 232px;
                border-radius: 18px;
            }

            .pm-header {
                position: sticky;
                top: 0;
                background: var(--white);
                z-index: 2;
                padding-bottom: 0.35rem;
            }

            .pm-header h3 {
                font-size: 1.55rem;
                line-height: 1.1;
            }

            .pm-desc {
                font-size: 0.95rem;
            }

            .pm-footer {
                bottom: -1rem;
                padding-bottom: env(safe-area-inset-bottom);
                gap: 0.55rem;
            }

            .pm-submit {
                font-size: 0.95rem;
                padding: 0.95rem 0.85rem;
                border-radius: 12px;
            }

            .pm-qty-control {
                padding: 0.2rem;
            }

            .pm-qty-btn {
                width: 28px;
                height: 28px;
            }

            .pm-qty-input {
                width: 30px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="top-header">
        <div class="header-left">
            <div class="brand-logo">KESHIR</div>
            <div class="date-time" id="datetime">Memuat waktu...</div>
        </div>
        <div class="header-right">
            <a href="{{ route('public.order-history') }}" class="icon-btn" title="Pesanan Saya" style="position:relative;text-decoration:none;">
                📋
                @if($myOrderCount > 0)
                <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:0.6rem;font-weight:800;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;">{{ $myOrderCount }}</span>
                @endif
            </a>
        </div>
    </header>

    {{-- ===== "We're Not Open Yet" overlay ===== --}}
    @if(!$isOpen)
    <div id="notOpenOverlay" style="
        position: fixed;
        inset: 0;
        z-index: 999;
        background: rgba(0,0,0,0.75);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
    ">
        <div style="
            background: #fff;
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        ">
            <div style="font-size:4rem;margin-bottom:1rem;">🕐</div>
            <h2 style="font-size:1.5rem;font-weight:800;margin-bottom:0.75rem;color:#1f2937;">We're Not Open Yet</h2>
            <p style="color:#6b7280;font-size:0.95rem;line-height:1.6;margin-bottom:1.5rem;">Maaf, kami belum buka saat ini. Kasir belum membuka shift. Silakan coba lagi nanti atau hubungi kami untuk informasi lebih lanjut.</p>
            <div style="padding:0.75rem 1.25rem;background:#fef3c7;border-radius:0.75rem;color:#92400e;font-size:0.85rem;font-weight:600;">⏰ Halaman ini akan otomatis refresh setiap 30 detik</div>
        </div>
    </div>
    <script>setTimeout(() => location.reload(), 30000);</script>
    @endif

    <div class="main-container">
        <!-- Left Area (Menu) -->
        <div class="content-area">
            <div class="search-bar">
                <span>🔍</span>
                <input type="text" id="searchInput" placeholder="Search menu...">
            </div>

            <!-- Categories -->
            <div class="category-scroll-wrapper">

                <div class="category-row" id="categoryRow">
                    <div class="category-card active" onclick="filterCategory('all')" id="cat-tab-all">
                        <div class="cat-img-box">
                            <div style="font-size:24px;">🍽️</div>
                        </div>
                        <div class="cat-text">
                            <div class="cat-name">Semua Menu</div>
                            <div class="cat-items">{{ $products->count() }} items</div>
                        </div>
                    </div>

                    @foreach($categories as $cat)
                    @php
                        $catName = strtolower($cat->name);
                        $icon = '🍽️';
                        if (str_contains($catName, 'kopi') || str_contains($catName, 'coffee')) $icon = '☕';
                        elseif (str_contains($catName, 'teh') || str_contains($catName, 'tea') || str_contains($catName, 'matcha')) $icon = '🍵';
                        elseif (str_contains($catName, 'makanan') || str_contains($catName, 'nasi') || str_contains($catName, 'mie')) $icon = '🍛';
                        elseif (str_contains($catName, 'cemilan') || str_contains($catName, 'snack') || str_contains($catName, 'pastry')) $icon = '🥐';
                        elseif (str_contains($catName, 'minuman') || str_contains($catName, 'beverage')) $icon = '🥤';
                    @endphp
                    <div class="category-card" onclick="filterCategory({{ $cat->id }})" id="cat-tab-{{ $cat->id }}">
                        <div class="cat-img-box">
                            <div style="font-size:24px;">{{ $icon }}</div>
                        </div>
                        <div class="cat-text">
                            <div class="cat-name">{{ strtoupper($cat->name) }}</div>
                            <div class="cat-items">{{ $products->where('category_id', $cat->id)->count() }} items</div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

            <!-- Products -->
            <div class="product-grid" id="productGrid">
                @foreach($products as $p)
                @php
                    $isOutOfStock = $p->is_out_of_stock;
                @endphp
                <div class="product-card" data-cat="{{ $p->category_id }}" data-name="{{ strtolower($p->name) }}" {!! $isOutOfStock ? 'style="opacity: 0.6; cursor: not-allowed;"' : 'onclick="openProductModal('.htmlspecialchars(json_encode($p)).')"' !!}>
                    <div class="product-image">
                        @if(is_array($p->photos) && count($p->photos) > 0)
                            <img src="{{ asset('storage/' . $p->photos[0]) }}" alt="{{ $p->name }}">
                        @else
                            <span style="font-size:3rem; opacity:0.1">🍽️</span>
                        @endif
                        @if($isOutOfStock)
                            <div style="position:absolute; top:8px; right:8px; background:var(--danger); color:white; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:bold;">Habis</div>
                        @endif
                    </div>
                    <div class="product-info">
                        <div class="product-name">{{ $p->name }}</div>
                        <div class="product-price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                        @if($p->variants && $p->variants->count() > 0)
                            <div class="product-variants">
                                @foreach($p->variants as $v)
                                    <span class="variant-tag">{{ $v->variant_name }} +Rp {{ number_format($v->additional_price, 0, ',', '.') }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <button class="add-btn" {!! $isOutOfStock ? 'disabled style="background:var(--text-muted);"' : '' !!}>+</button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right Area (Sidebar / Cart) -->
        <div class="sidebar">
            <button type="button" class="sidebar-mobile-close" onclick="toggleMobileCart(false)">×</button>
            <form id="checkoutForm" onsubmit="event.preventDefault(); openPaymentModal();">
                <div class="sidebar-header">
                    <h2>Struk Belanja</h2>
                    <p>#{{ strtoupper(substr(uniqid(), -8)) }}</p>
                </div>

                <div class="order-toggle">
                    <input type="hidden" id="order_type" name="order_type" value="dine_in">
                    <button type="button" class="order-type-btn active" id="btnDineIn" onclick="setOrderType('dine_in')">Dine In</button>
                    <button type="button" class="order-type-btn" id="btnTakeAway" onclick="setOrderType('takeaway')">Take Away</button>
                    <button type="button" class="order-type-btn" id="btnBooking" onclick="setOrderType('booking')">Booking</button>
                </div>

                <div class="sidebar-scrollable">
                <div class="customer-form">
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Nama..." required>
                    </div>
                    <div class="form-group" id="mejaGroup">
                        <label>Meja</label>
                        <select name="table_id" id="table_id" required>
                            <option value="">Pilih...</option>
                            @foreach($tables as $t)
                                <option value="{{ $t->id }}">{{ $t->table_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Required for Midtrans -->
                    <div class="form-group-full">
                        <input type="tel" name="phone" id="phone" placeholder="No HP Pelanggan" required>
                    </div>
                    <!-- Booking fields -->
                    <div class="form-group-full" id="bookingTimeGroup" style="display:none;">
                        <label style="display:block;font-size:0.8rem;font-weight:600;margin-bottom:0.4rem;">📅 Waktu Kedatangan</label>
                        <input type="datetime-local" name="booking_time" id="booking_time" style="width:100%;padding:0.6rem 0.75rem;border:1px solid var(--border-color);border-radius:50px;font-size:0.85rem;outline:none;background:var(--white);">
                    </div>
                    <div class="form-group-full" id="peopleCountGroup" style="display:none;">
                        <label style="display:block;font-size:0.8rem;font-weight:600;margin-bottom:0.4rem;">👥 Jumlah Orang</label>
                        <input type="number" name="people_count" id="people_count" value="1" min="1" max="20" style="width:100%;padding:0.6rem 0.75rem;border:1px solid var(--border-color);border-radius:50px;font-size:0.85rem;outline:none;background:var(--white);">
                    </div>
                    <!-- Hidden fields for process -->
                    <input type="hidden" name="payment_method" id="payment_method" value="">
                </div>

                <div class="cart-title">List Pesanan</div>
                <div class="cart-items" id="cartContainer">
                    @forelse($cart as $id => $item)
                    <div class="cart-item">
                        <div class="cart-item-img">
                            @if(isset($item['image']) && $item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="IMG">
                            @else
                                <span style="font-size:1.5rem">🍽️</span>
                            @endif
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">{{ $item['product_name'] ?? 'Unknown Product' }}</div>
                            <div class="cart-item-price">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</div>
                            @if(!empty($item['variant_name']))
                                <div class="cart-item-meta">☑ {{ $item['variant_name'] }}</div>
                            @endif
                            @if(!empty($item['addons']))
                                @foreach($item['addons'] as $addon)
                                    <div class="cart-item-meta">+ {{ $addon['name'] }}</div>
                                @endforeach
                            @endif
                        </div>
                        <div class="qty-control">
                            <button type="button" class="qty-btn" style="color:var(--danger)" onclick="updateCartQty('{{ $id }}', {{ $item['qty'] - 1 }})">−</button>
                            <input type="text" class="qty-input" value="{{ $item['qty'] }}" readonly>
                            <button type="button" class="qty-btn" style="color:var(--primary)" onclick="updateCartQty('{{ $id }}', {{ $item['qty'] + 1 }})">+</button>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center; padding: 2rem; color: var(--text-muted); font-size: 0.85rem;">
                        Belum ada pesanan.<br>Pilih menu di sebelah kiri.
                    </div>
                    @endforelse
                </div>
                </div>

                <div class="sidebar-footer">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($cartSummary['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Pajak - {{ $taxRate }}%</span>
                        <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <button type="submit" class="place-order-btn" {{ empty($cart) ? 'disabled' : '' }}>
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mobile-cart-backdrop" id="mobileCartBackdrop" onclick="toggleMobileCart(false)"></div>
    <div class="mobile-cart-bar">
        <div class="mobile-cart-total">
            <span>Total</span>
            <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
        </div>
        <button type="button" class="mobile-cart-open-btn" onclick="toggleMobileCart(true)">→</button>
    </div>

    <!-- Chatbot FAB Button -->
    <button type="button" class="chatbot-fab" id="chatbotFab" onclick="toggleChatPanel(true)" aria-label="Chatbot AI">
        <span class="chatbot-fab-icon">🤖</span>
        <span class="chatbot-fab-label">Tanya saya!</span>
    </button>

    <!-- Chat Panel -->
    <div class="chat-panel" id="chatPanel">
        <div class="chat-header">
            <div class="chat-header-avatar">🤖</div>
            <div class="chat-header-info">
                <h4>Asisten Keshir</h4>
                <span>AI Pelayan Virtual • Siap membantu!</span>
            </div>
            <button class="chat-reset-btn" onclick="resetChat()" title="Reset Chat">🗑️</button>
            <button class="chat-close-btn" onclick="toggleChatPanel(false)">✕</button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <!-- Greeting will be injected by JS -->
        </div>
        <div class="chat-quick-replies" id="chatQuickReplies">
            <button class="quick-reply-btn" onclick="sendQuickReply('📋 Lihat semua menu')">📋 Menu</button>
            <button class="quick-reply-btn" onclick="sendQuickReply('⭐ Apa menu terlaris?')">⭐ Terlaris</button>
            <button class="quick-reply-btn" onclick="sendQuickReply('💰 Berapa pajak pesanan?')">💰 Pajak</button>
            <button class="quick-reply-btn" onclick="sendQuickReply('🎁 Ada promo aktif?')">🎁 Promo</button>
            <button class="quick-reply-btn" onclick="sendQuickReply('🪑 Ada meja kosong?')">🪑 Meja</button>
            <button class="quick-reply-btn" onclick="sendQuickReply('🕐 Jam buka kapan?')">🕐 Jam Buka</button>
        </div>
        <div class="chat-input-area">
            <input type="text" class="chat-input" id="chatInput" placeholder="Ketik pesan..." autocomplete="off">
            <button class="chat-send-btn" id="chatSendBtn" onclick="sendChatMessage()">➤</button>
        </div>
    </div>

    <!-- Payment Choice Modal -->
    <div class="payment-modal" id="paymentModal" onclick="if(event.target===this) closePaymentModal()">
        <div style="position: absolute; top: 20px; right: 20px;">
            <button onclick="closePaymentModal()" style="background:var(--danger); color:white; border:none; padding: 0.5rem 1rem; border-radius:8px; cursor:pointer; font-weight:bold;">Tutup</button>
        </div>
        <div class="payment-content">
            <button class="payment-btn" onclick="submitCheckout('tunai')" id="btnTunaiModal">TUNAI</button>
            <button class="payment-btn" onclick="submitCheckout('digital')" id="btnMidtrans">MIDTRANS</button>
        </div>
    </div>

    <!-- Product Add Modal -->
    <div class="product-modal" id="productModal" onclick="if(event.target===this) closeProductModal()">
        <div class="pm-content">
            <div class="pm-header">
                <h3 id="pm-title">Product</h3>
                <button class="pm-close" onclick="closeProductModal()">×</button>
            </div>

            <div class="pm-hero" id="pm-hero">
                <span class="pm-hero-fallback" id="pm-hero-fallback">🍽️</span>
                <img id="pm-image" alt="Detail Produk" style="display:none;">
            </div>

            <p class="pm-desc" id="pm-desc">Pilih varian dan tambah catatan sesuai selera kamu.</p>
            
            <form id="addToCartForm" action="{{ route('public.addToCart') }}">
                <input type="hidden" name="product_id" id="pm-pid">
                
                <div class="pm-group" id="pm-v-group" style="display:none">
                    <label>Varian (Wajib)</label>
                    <div class="radio-grid" id="pm-variants"></div>
                </div>
                
                <div class="pm-group" id="pm-a-group" style="display:none">
                    <label>Addons (Opsional)</label>
                    <div class="radio-grid" id="pm-addons"></div>
                </div>

                <div class="pm-group">
                    <label>Catatan</label>
                    <textarea name="notes" class="pm-notes" placeholder="Contoh: less sugar, no ice, dll..."></textarea>
                </div>

                <div class="pm-divider"></div>
                <div class="pm-footer">
                    <div class="pm-qty-control">
                        <button type="button" class="pm-qty-btn" onclick="let q=document.getElementById('pm-qty'); if(q.value>1)q.value--">−</button>
                        <input type="number" name="qty" id="pm-qty" value="1" min="1" class="pm-qty-input" readonly>
                        <button type="button" class="pm-qty-btn" onclick="let q=document.getElementById('pm-qty'); q.value++">+</button>
                    </div>
                    <button type="button" class="pm-submit" style="margin-top:0;" onclick="submitAddToCart()">Tambah ke Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Submit form handler & Scripts -->
    <script>
        // Update Time
        function updateTime() {
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            document.getElementById('datetime').innerText = 
                `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Restore and Save Form State
        document.addEventListener('DOMContentLoaded', () => {
            const fieldsToSave = ['customer_name', 'table_id', 'phone', 'booking_time', 'people_count', 'order_type'];
            fieldsToSave.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    // Restore from session storage
                    const savedValue = sessionStorage.getItem('keshir_cart_' + field);
                    if (savedValue !== null) {
                        element.value = savedValue;
                        // Special handling for order_type restoration
                        if (field === 'order_type') {
                            setOrderType(savedValue);
                        }
                    }
                    // Save on change
                    element.addEventListener('input', () => {
                        sessionStorage.setItem('keshir_cart_' + field, element.value);
                    });
                    element.addEventListener('change', () => {
                        sessionStorage.setItem('keshir_cart_' + field, element.value);
                    });
                }
            });
        });


        // Search Handlers
        document.getElementById('searchInput').addEventListener('input', function(e) {
            let term = e.target.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                if(card.getAttribute('data-name').includes(term)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Category Filter
        function filterCategory(catId) {
            document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
            if(catId === 'all') {
                document.querySelector('.category-card').classList.add('active'); // First child is 'all'
                document.querySelectorAll('.product-card').forEach(c => c.style.display = 'block');
            } else {
                document.getElementById('cat-tab-' + catId).classList.add('active');
                document.querySelectorAll('.product-card').forEach(c => {
                    c.style.display = c.getAttribute('data-cat') == catId ? 'block' : 'none';
                });
            }
        }

        // Hardcoded category filtering when database categories are not set up
        function filterCategoryByText(type) {
            document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
            document.getElementById('cat-tab-' + type).classList.add('active');
            
            document.querySelectorAll('.product-card').forEach(c => {
                let name = c.getAttribute('data-name').toLowerCase();
                let match = false;
                if (type === 'kopi' && (name.includes('kopi') || name.includes('coffee') || name.includes('latte') || name.includes('espresso') || name.includes('americano') || name.includes('cappuccino') || name.includes('mocha'))) {
                    match = true;
                } else if (type === 'teh' && (name.includes('teh') || name.includes('tea') || name.includes('matcha') || name.includes('oolong') || name.includes('chamomile'))) {
                    match = true;
                } else if (type === 'snack' && (name.includes('snack') || name.includes('kue') || name.includes('roti') || name.includes('fries') || name.includes('kentang') || name.includes('croissant') || name.includes('pastry') || name.includes('cake') || name.includes('tart'))) {
                    match = true;
                }
                c.style.display = match ? 'block' : 'none';
            });
        }

        // Category Scroll Navigation
        function scrollCategories(direction) {
            const row = document.getElementById('categoryRow');
            if (!row) return;
            const cardWidth = row.querySelector('.category-card')?.offsetWidth || 250;
            row.scrollBy({ left: direction * (cardWidth + 12) * 2, behavior: 'smooth' });
        }

        function updateCatScrollArrows() {
            const row = document.getElementById('categoryRow');
            const leftBtn = document.getElementById('catScrollLeft');
            const rightBtn = document.getElementById('catScrollRight');
            if (!row || !leftBtn || !rightBtn) return;

            leftBtn.classList.toggle('hidden', row.scrollLeft <= 5);
            rightBtn.classList.toggle('hidden', row.scrollLeft + row.clientWidth >= row.scrollWidth - 5);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const row = document.getElementById('categoryRow');
            if (row) {
                row.addEventListener('scroll', updateCatScrollArrows);
                // Enable mouse wheel horizontal scroll
                row.addEventListener('wheel', function(e) {
                    if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                        e.preventDefault();
                        row.scrollBy({ left: e.deltaY, behavior: 'auto' });
                    }
                }, { passive: false });
                // Initial arrow state
                updateCatScrollArrows();
            }
        });

        // Toggle Dine In / Take Away / Booking
        function setOrderType(type) {
            document.getElementById('order_type').value = type;
            const btnDineIn = document.getElementById('btnDineIn');
            const btnTakeAway = document.getElementById('btnTakeAway');
            const btnBooking = document.getElementById('btnBooking');
            const mejaGroup = document.getElementById('mejaGroup');
            const tableId = document.getElementById('table_id');
            const bookingTimeGroup = document.getElementById('bookingTimeGroup');
            const bookingTime = document.getElementById('booking_time');
            const peopleCountGroup = document.getElementById('peopleCountGroup');
            
            // Reset all buttons
            btnDineIn.classList.remove('active');
            btnTakeAway.classList.remove('active');
            btnBooking.classList.remove('active');
            
            if (type === 'dine_in') {
                btnDineIn.classList.add('active');
                mejaGroup.style.display = 'block';
                tableId.required = true;
                bookingTimeGroup.style.display = 'none';
                peopleCountGroup.style.display = 'none';
                bookingTime.required = false;
                bookingTime.value = '';
            } else if (type === 'takeaway') {
                btnTakeAway.classList.add('active');
                mejaGroup.style.display = 'none';
                tableId.required = false;
                tableId.value = '';
                bookingTimeGroup.style.display = 'none';
                peopleCountGroup.style.display = 'none';
                bookingTime.required = false;
                bookingTime.value = '';
            } else if (type === 'booking') {
                btnBooking.classList.add('active');
                mejaGroup.style.display = 'block';
                tableId.required = true;
                bookingTimeGroup.style.display = 'block';
                peopleCountGroup.style.display = 'block';
                bookingTime.required = true;
                // Set min booking time to now + 1 hour (using local timezone, not UTC)
                const now = new Date();
                now.setHours(now.getHours() + 1);
                const pad = n => String(n).padStart(2, '0');
                const minTime = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                bookingTime.min = minTime;
                if (!bookingTime.value) bookingTime.value = minTime;
            }
        }

        const storageBaseUrl = @json(asset('storage'));

        function getProductImageUrl(product) {
            if (!product || !Array.isArray(product.photos) || product.photos.length === 0) return null;
            const firstPhoto = String(product.photos[0] || '');
            if (!firstPhoto) return null;
            if (firstPhoto.startsWith('http://') || firstPhoto.startsWith('https://') || firstPhoto.startsWith('/')) {
                return firstPhoto;
            }
            return `${storageBaseUrl}/${firstPhoto}`;
        }

        // Add Product Modal
        function openProductModal(product) {
            document.getElementById('pm-title').innerText = product.name;
            document.getElementById('pm-pid').value = product.id;
            document.getElementById('pm-qty').value = 1;

            const pmImage = document.getElementById('pm-image');
            const pmFallback = document.getElementById('pm-hero-fallback');
            const imageUrl = getProductImageUrl(product);
            if (imageUrl) {
                pmImage.src = imageUrl;
                pmImage.style.display = 'block';
                pmFallback.style.display = 'none';
            } else {
                pmImage.src = '';
                pmImage.style.display = 'none';
                pmFallback.style.display = 'block';
            }

            const defaultDesc = `Nikmati ${product.name} dengan rasa terbaik ala Keshir. Bisa pilih varian dan custom catatan sesuai selera.`;
            document.getElementById('pm-desc').innerText = product.description || defaultDesc;

            const vGroup = document.getElementById('pm-v-group');
            const vGrid = document.getElementById('pm-variants');
            if(product.variants && product.variants.length > 0) {
                vGroup.style.display = 'block';
                vGrid.innerHTML = product.variants.map((v, i) => `
                    <label class="radio-card">
                        <span class="rc-left"><input type="radio" name="product_variant_id" value="${v.id}" ${i===0?'checked':''}> <span style="font-weight:600">${v.variant_name}</span></span>
                        <span class="rc-price">+Rp ${new Intl.NumberFormat('id-ID').format(v.additional_price)}</span>
                    </label>
                `).join('');
            } else {
                vGroup.style.display = 'none';
                vGrid.innerHTML = '';
            }

            const aGroup = document.getElementById('pm-a-group');
            const aGrid = document.getElementById('pm-addons');
            if(product.addons && product.addons.length > 0) {
                aGroup.style.display = 'block';
                aGrid.innerHTML = product.addons.map(a => `
                    <label class="radio-card">
                        <span class="rc-left"><input type="checkbox" name="addons[]" value="${a.id}"> <span style="font-weight:600">${a.addon_name}</span></span>
                        <span class="rc-price">+Rp ${new Intl.NumberFormat('id-ID').format(a.price)}</span>
                    </label>
                `).join('');
            } else {
                aGroup.style.display = 'none';
                aGrid.innerHTML = '';
            }

            const notesEl = document.querySelector('#addToCartForm textarea[name="notes"]');
            if (notesEl) notesEl.value = '';

            document.getElementById('productModal').classList.add('show');
        }

        function closeProductModal() { document.getElementById('productModal').classList.remove('show'); }

        function submitAddToCart() {
            const form = document.getElementById('addToCartForm');
            const formData = new FormData(form);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload(); // Simple reload to update cart UI instead of complex DOM diffing
                }
            })
            .catch(err => console.error(err));
        }

        // Handle quantity update (+/-)
        function updateCartQty(cartId, newQty) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            
            if (newQty <= 0) {
                // If qty becomes 0, remove item
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('cart_item_id', cartId);
                fetch("{{ route('public.removeFromCart') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                }).then(() => window.location.reload());
            } else {
                // Otherwise update item qty
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('cart_item_id', cartId);
                formData.append('qty', newQty);
                fetch("{{ route('public.updateCart') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                }).then(() => window.location.reload());
            }
        }

        function handleRemove(e, form) {
            // let normal submission happen
            return true;
        }

        // Checkout Modals
        function openPaymentModal() {
            // Check HTML5 validity first
            if (!document.getElementById('checkoutForm').checkValidity()) {
                document.getElementById('checkoutForm').reportValidity();
                return;
            }

            // Bug fix: Validate table is selected for dine_in and booking
            const orderType = document.getElementById('order_type').value;
            const tableId = document.getElementById('table_id').value;
            if ((orderType === 'dine_in' || orderType === 'booking') && !tableId) {
                alert('Silakan pilih meja terlebih dahulu.');
                return;
            }
            if (orderType === 'booking') {
                const bookingTime = document.getElementById('booking_time').value;
                if (!bookingTime) {
                    alert('Silakan isi waktu kedatangan untuk booking.');
                    return;
                }
                // Booking: skip payment modal, submit directly for cashier validation
                if (confirm('Kirim reservasi booking? Kasir akan mengonfirmasi reservasi Anda terlebih dahulu sebelum pembayaran.')) {
                    submitCheckout('booking');
                }
                return;
            } else {
                const btnTunai = document.getElementById('btnTunaiModal');
                if(btnTunai) btnTunai.style.display = 'inline-block';
            }

            document.getElementById('paymentModal').classList.add('show');
        }
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('show');
        }

        function submitCheckout(paymentMethod) {
            document.getElementById('payment_method').value = paymentMethod;
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('payment_method', paymentMethod);

            // Bug fix: Remove table_id for takeaway to prevent backend validation error
            if (formData.get('order_type') === 'takeaway') {
                formData.delete('table_id');
                formData.delete('people_count');
            }
            
            closePaymentModal();
            
            if (paymentMethod === 'tunai') {
                // To keep it simple without changing CheckoutController significantly, we just submit.
                // The CheckoutController currently requests midtrans. Since user requested Tunai logic 
                // in the image, we can just POST and catch the SnapToken, but since it's tunai, just redirect to order-status.
                formData.append('is_cash', 'true');
            }

            const btnMidtrans = document.getElementById('btnMidtrans');
            btnMidtrans.innerText = "Membuka...";
            btnMidtrans.disabled = true;

            fetch('{{ route("public.checkout.process") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => {
                if (r.status === 422) {
                    // Validation error
                    return r.json().then(data => {
                        let errorMsg = '⚠️ Kesalahan input:\n';
                        if (data.errors) {
                            for (const key in data.errors) {
                                errorMsg += `- ${data.errors[key][0]}\n`;
                            }
                        } else {
                            errorMsg = data.message || 'Validasi gagal.';
                        }
                        alert(errorMsg);
                        btnMidtrans.innerText = "MIDTRANS";
                        btnMidtrans.disabled = false;
                        return null;
                    });
                }
                return r.json();
            })
            .then(res => {
                if (!res) return; // Validation error already handled

                if (paymentMethod === 'tunai' && res.redirect_url) {
                     sessionStorage.clear();
                     window.location.href = res.redirect_url;
                     return;
                }

                if(res.status === 'success' && res.snap_token) {
                    // Midtrans Mode
                    snap.pay(res.snap_token, {
                        onSuccess: function(result){ 
                            sessionStorage.clear();
                            window.location.href = res.redirect_url; 
                        },
                        onPending: function(result){ 
                            sessionStorage.clear();
                            window.location.href = res.redirect_url; 
                        },
                        onError: function(result){ alert('Pembayaran gagal.'); window.location.reload(); },
                        onClose: function(){
                            btnMidtrans.innerText = "MIDTRANS";
                            btnMidtrans.disabled = false;
                        }
                    });
                } else if(res.status === 'success' && res.redirect_url) {
                    // Bypass mode if backend implemented it
                    sessionStorage.clear();
                    window.location.href = res.redirect_url;
                } else {
                    alert(res.message || 'Terjadi kesalahan.');
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan sistem.');
                window.location.reload();
            });
        }

        function toggleMobileCart(show) {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('mobileCartBackdrop');
            if (!sidebar || !backdrop) return;

            const mobileMode = window.matchMedia('(max-width: 992px)').matches;
            if (!mobileMode) return;

            sidebar.classList.toggle('open', show);
            backdrop.classList.toggle('show', show);
            document.body.style.overflow = show ? 'hidden' : '';
        }

        window.addEventListener('resize', () => {
            const mobileMode = window.matchMedia('(max-width: 992px)').matches;
            if (!mobileMode) {
                const sidebar = document.querySelector('.sidebar');
                const backdrop = document.getElementById('mobileCartBackdrop');
                if (sidebar) sidebar.classList.remove('open');
                if (backdrop) backdrop.classList.remove('show');
                document.body.style.overflow = '';
            }
        });

        // =============================================
        // CHATBOT AI — Full Chat Widget (Enhanced)
        // =============================================
        const chatPanel = document.getElementById('chatPanel');
        const chatFab = document.getElementById('chatbotFab');
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('chatInput');
        const chatSendBtn = document.getElementById('chatSendBtn');
        const CHAT_STORAGE_KEY = 'keshir_chat_history';
        const CHAT_CONV_KEY = 'keshir_conversation';
        let conversationHistory = [];
        let isSending = false;

        // --- Sound Notification ---
        const chatNotificationSound = (function() {
            // Generate a short beep using Web Audio API
            let audioCtx = null;
            return function() {
                try {
                    if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
                    gainNode.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
                    oscillator.start(audioCtx.currentTime);
                    oscillator.stop(audioCtx.currentTime + 0.3);
                } catch(e) { /* ignore audio errors */ }
            };
        })();

        // --- Contextual Greeting ---
        function getContextualGreeting() {
            const hour = new Date().getHours();
            let timeGreeting, emoji, suggestion;
            if (hour >= 5 && hour < 11) {
                timeGreeting = 'Selamat pagi';
                emoji = '🌅';
                suggestion = 'Mau mulai hari dengan secangkir kopi hangat?';
            } else if (hour >= 11 && hour < 15) {
                timeGreeting = 'Selamat siang';
                emoji = '☀️';
                suggestion = 'Mau pesan minuman segar untuk menemani makan siang?';
            } else if (hour >= 15 && hour < 18) {
                timeGreeting = 'Selamat sore';
                emoji = '🌤️';
                suggestion = 'Waktunya coffee break! Ada yang bisa saya bantu?';
            } else {
                timeGreeting = 'Selamat malam';
                emoji = '🌙';
                suggestion = 'Mau pesan minuman hangat untuk menemani malam Anda?';
            }
            return `${timeGreeting}! ${emoji} Saya asisten virtual <strong>Keshir Coffee Shop</strong>.<br><br>${suggestion}<br>Silakan ketik atau pilih tombol di bawah! ☕`;
        }

        // --- LocalStorage Persistence ---
        function saveChat() {
            try {
                const bubbles = chatMessages.querySelectorAll('.chat-bubble');
                const messages = [];
                bubbles.forEach(b => {
                    messages.push({
                        type: b.classList.contains('user') ? 'user' : 'bot',
                        html: b.innerHTML
                    });
                });
                localStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(messages));
                localStorage.setItem(CHAT_CONV_KEY, JSON.stringify(conversationHistory));
            } catch(e) {}
        }

        function loadChat() {
            try {
                const saved = localStorage.getItem(CHAT_STORAGE_KEY);
                const savedConv = localStorage.getItem(CHAT_CONV_KEY);
                if (saved) {
                    const messages = JSON.parse(saved);
                    if (messages.length > 0) {
                        chatMessages.innerHTML = '';
                        messages.forEach(m => {
                            const bubble = document.createElement('div');
                            bubble.className = 'chat-bubble ' + m.type;
                            bubble.innerHTML = m.html;
                            chatMessages.appendChild(bubble);
                        });
                        // Re-attach action button listeners
                        attachCartActionListeners();
                    } else {
                        injectGreeting();
                    }
                } else {
                    injectGreeting();
                }
                if (savedConv) {
                    conversationHistory = JSON.parse(savedConv);
                }
            } catch(e) {
                injectGreeting();
            }
        }

        function injectGreeting() {
            chatMessages.innerHTML = '';
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble bot';
            bubble.innerHTML = getContextualGreeting();
            chatMessages.appendChild(bubble);
        }

        // --- Reset Chat ---
        function resetChat() {
            conversationHistory = [];
            localStorage.removeItem(CHAT_STORAGE_KEY);
            localStorage.removeItem(CHAT_CONV_KEY);
            injectGreeting();
        }

        // --- Toggle Chat Panel ---
        function toggleChatPanel(show) {
            if (show) {
                chatPanel.classList.add('open');
                chatFab.classList.add('hidden');
                setTimeout(() => chatInput.focus(), 350);
            } else {
                chatPanel.classList.remove('open');
                chatFab.classList.remove('hidden');
            }
        }

        // Send on Enter key
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendChatMessage();
            }
        });

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function addBubble(text, type) {
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble ' + type;
            if (type === 'bot') {
                bubble.innerHTML = renderMarkdown(text);
            } else {
                bubble.textContent = text;
            }
            chatMessages.appendChild(bubble);
            scrollToBottom();
            saveChat();
            return bubble;
        }

        function showTyping() {
            const el = document.createElement('div');
            el.className = 'typing-indicator';
            el.id = 'typingIndicator';
            el.innerHTML = '<span></span><span></span><span></span>';
            chatMessages.appendChild(el);
            scrollToBottom();
        }

        function hideTyping() {
            const el = document.getElementById('typingIndicator');
            if (el) el.remove();
        }

        // --- Improved Markdown Renderer (with list support) ---
        function renderMarkdown(text) {
            if (!text) return '';

            // Split by lines for block-level processing
            const lines = text.split('\n');
            let html = '';
            let inList = false;
            let listType = ''; // 'ul' or 'ol'

            for (let i = 0; i < lines.length; i++) {
                let line = lines[i];

                // Numbered list: "1. item", "2. item"
                const olMatch = line.match(/^\d+\.\s+(.+)/);
                // Bullet list: "- item", "* item", "• item"
                const ulMatch = line.match(/^[-*•]\s+(.+)/);

                if (olMatch) {
                    if (!inList || listType !== 'ol') {
                        if (inList) html += listType === 'ul' ? '</ul>' : '</ol>';
                        html += '<ol style="margin:0.3rem 0 0.3rem 1.2rem;padding:0;">';
                        inList = true;
                        listType = 'ol';
                    }
                    html += '<li>' + inlineMarkdown(olMatch[1]) + '</li>';
                } else if (ulMatch) {
                    if (!inList || listType !== 'ul') {
                        if (inList) html += listType === 'ul' ? '</ul>' : '</ol>';
                        html += '<ul style="margin:0.3rem 0 0.3rem 1.2rem;padding:0;">';
                        inList = true;
                        listType = 'ul';
                    }
                    html += '<li>' + inlineMarkdown(ulMatch[1]) + '</li>';
                } else {
                    if (inList) {
                        html += listType === 'ul' ? '</ul>' : '</ol>';
                        inList = false;
                        listType = '';
                    }
                    html += (line.trim() === '' ? '<br>' : inlineMarkdown(line) + '<br>');
                }
            }
            if (inList) {
                html += listType === 'ul' ? '</ul>' : '</ol>';
            }

            // Clean up trailing <br>
            html = html.replace(/(<br>)+$/, '');
            return html;
        }

        function inlineMarkdown(text) {
            return text
                .replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1" loading="lazy" style="max-width:100%;border-radius:8px;margin:0.3rem 0;">')
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');
        }

        // --- Quick Reply ---
        function sendQuickReply(msg) {
            chatInput.value = msg;
            sendChatMessage();
        }

        // --- Add to Cart from Chat ---
        function attachCartActionListeners() {
            document.querySelectorAll('.chat-action-btn[data-product-name]').forEach(btn => {
                btn.onclick = function() {
                    const name = this.dataset.productName;
                    // Find matching product card and click it
                    const cards = document.querySelectorAll('.product-card');
                    for (const card of cards) {
                        if (card.dataset.name && card.dataset.name.includes(name.toLowerCase())) {
                            card.click();
                            toggleChatPanel(false);
                            return;
                        }
                    }
                    alert('Menu "' + name + '" tidak ditemukan di halaman. Silakan cari di menu.');
                };
            });
        }

        function injectCartButtons(botBubble, botText) {
            // Try to detect menu names mentioned in the response
            // Match patterns like: "Kopi Kapal Api", product names from the page
            const productCards = document.querySelectorAll('.product-card');
            const productNames = [];
            productCards.forEach(card => {
                const name = card.dataset.name;
                if (name) productNames.push(name);
            });

            const lowerText = botText.toLowerCase();
            const matched = [];
            productNames.forEach(name => {
                if (lowerText.includes(name) && !matched.includes(name)) {
                    matched.push(name);
                }
            });

            if (matched.length > 0 && matched.length <= 5) {
                const container = document.createElement('div');
                container.style.marginTop = '0.5rem';
                matched.forEach(name => {
                    // Capitalize for display
                    const displayName = name.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                    const btn = document.createElement('button');
                    btn.className = 'chat-action-btn';
                    btn.dataset.productName = name;
                    btn.textContent = '🛒 Pesan ' + displayName;
                    btn.onclick = function() {
                        const cards = document.querySelectorAll('.product-card');
                        for (const card of cards) {
                            if (card.dataset.name && card.dataset.name.includes(name)) {
                                card.click();
                                toggleChatPanel(false);
                                return;
                            }
                        }
                    };
                    container.appendChild(btn);
                    container.appendChild(document.createTextNode(' '));
                });
                botBubble.appendChild(container);
            }
        }

        // --- Main Send Function ---
        async function sendChatMessage() {
            const msg = chatInput.value.trim();
            if (!msg || isSending) return;

            isSending = true;
            chatInput.value = '';
            chatSendBtn.disabled = true;

            addBubble(msg, 'user');
            conversationHistory.push({ role: 'user', content: msg });

            showTyping();

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('/api/v1/chatbot/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: msg,
                        conversation_history: conversationHistory.slice(-10),
                        role: 'customer'
                    })
                });

                hideTyping();

                const result = await response.json();

                if (result.success && result.data && result.data.message) {
                    const botText = result.data.message;
                    const botBubble = addBubble(botText, 'bot');
                    conversationHistory.push({ role: 'assistant', content: botText });

                    // Try to add "add to cart" buttons
                    injectCartButtons(botBubble, botText);

                    // Sound notification
                    chatNotificationSound();
                } else {
                    addBubble(result.data?.message || 'Maaf, terjadi kesalahan. Coba lagi ya! 🙏', 'bot');
                }

                saveChat();
            } catch (err) {
                hideTyping();
                console.error('Chatbot error:', err);
                addBubble('Maaf, tidak bisa terhubung ke server AI. Pastikan Ollama sedang berjalan. 🔌', 'bot');
            } finally {
                isSending = false;
                chatSendBtn.disabled = false;
                chatInput.focus();
            }
        }

        // --- Initialize ---
        loadChat();
    </script>
</body>
</html>
