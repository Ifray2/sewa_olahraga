<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SportRent') }} — Petugas</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,700;1,300&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            :root {
                --black:   #0a0a0a;
                --surface: #111111;
                --card:    #1a1a1a;
                --border:  rgba(245,243,238,0.07);
                --white:   #f5f3ee;
                --acid:    #c8f542;
                --orange:  #f55a00;
                --mid:     #6b6b6b;
                --muted:   rgba(245,243,238,0.45);
                --sidebar-w: 260px;
                --font-display: 'Bebas Neue', sans-serif;
                --font-body:    'DM Sans', sans-serif;
            }

            html, body {
                height: 100%;
                background: var(--black);
                color: var(--white);
                font-family: var(--font-body);
                font-weight: 300;
                font-size: 14px;
                overflow-x: hidden;
            }

            /* ─────────────────────────────── LAYOUT ─── */
            .admin-wrapper {
                display: flex;
                min-height: 100vh;
            }

            /* ─────────────────────────────── SIDEBAR ─── */
            .sidebar {
                width: var(--sidebar-w);
                background: var(--surface);
                border-right: 1px solid var(--border);
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 0; left: 0; bottom: 0;
                z-index: 50;
                transition: transform 0.3s ease;
                overflow-y: auto;
                scrollbar-width: none;
            }
            .sidebar::-webkit-scrollbar { display: none; }

            .sidebar-logo {
                padding: 1.6rem 1.5rem 1.2rem;
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                gap: 0.6rem;
                text-decoration: none;
            }
            .sidebar-logo-text {
                font-family: var(--font-display);
                font-size: 1.6rem;
                color: var(--white);
                letter-spacing: 0.03em;
            }
            .sidebar-logo-text span { color: var(--acid); }
            .sidebar-badge {
                font-size: 0.6rem;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                background: var(--acid);
                color: var(--black);
                padding: 0.15rem 0.5rem;
                border-radius: 2px;
                margin-left: auto;
            }

            .sidebar-nav {
                flex: 1;
                padding: 1rem 0;
            }

            .nav-section-label {
                font-size: 0.62rem;
                font-weight: 500;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: var(--mid);
                padding: 1rem 1.5rem 0.4rem;
            }

            .nav-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.65rem 1.5rem;
                color: var(--muted);
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 400;
                position: relative;
                transition: color 0.15s, background 0.15s;
                border-left: 2px solid transparent;
            }
            .nav-item:hover {
                color: var(--white);
                background: rgba(200,245,66,0.04);
            }
            .nav-item.active {
                color: var(--acid);
                background: rgba(200,245,66,0.07);
                border-left-color: var(--acid);
                font-weight: 500;
            }
            .nav-item .nav-icon {
                width: 1.1rem;
                font-size: 1rem;
                text-align: center;
                flex-shrink: 0;
            }
            .nav-item .nav-badge {
                margin-left: auto;
                background: var(--acid);
                color: var(--black);
                font-size: 0.6rem;
                font-weight: 700;
                padding: 0.1rem 0.45rem;
                border-radius: 20px;
                min-width: 1.3rem;
                text-align: center;
            }
            .nav-item .nav-badge.orange {
                background: var(--orange);
                color: #fff;
            }

            .sidebar-footer {
                padding: 1rem 1.5rem 1.5rem;
                border-top: 1px solid var(--border);
            }
            .sidebar-user {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.75rem;
                border-radius: 4px;
                background: rgba(245,243,238,0.04);
                cursor: pointer;
                transition: background 0.15s;
                position: relative;
            }
            .sidebar-user:hover { background: rgba(245,243,238,0.07); }
            .user-avatar {
                width: 2.2rem; height: 2.2rem;
                border-radius: 50%;
                background: var(--acid);
                color: var(--black);
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: var(--font-display);
                font-size: 1rem;
                flex-shrink: 0;
            }
            .user-info { flex: 1; min-width: 0; }
            .user-name {
                font-size: 0.82rem;
                font-weight: 500;
                color: var(--white);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .user-role {
                font-size: 0.68rem;
                color: var(--mid);
                letter-spacing: 0.06em;
                text-transform: uppercase;
                margin-top: 0.1rem;
            }
            .user-role.admin   { color: var(--acid); }
            .user-role.petugas { color: #60a5fa; }

            /* ─────────────────────────────── TOPBAR ─── */
            .main-content {
                margin-left: var(--sidebar-w);
                flex: 1;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .topbar {
                height: 60px;
                background: var(--surface);
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 2rem;
                position: sticky;
                top: 0;
                z-index: 40;
                gap: 1rem;
            }

            .topbar-left {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .sidebar-toggle {
                display: none;
                background: none;
                border: none;
                color: var(--muted);
                cursor: pointer;
                font-size: 1.3rem;
                padding: 0.2rem;
                transition: color 0.15s;
            }
            .sidebar-toggle:hover { color: var(--white); }

            .topbar-heading {
                font-family: var(--font-display);
                font-size: 1.5rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--white);
            }

            /* Breadcrumb */
            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 0.4rem;
                font-size: 0.75rem;
                color: var(--mid);
            }
            .breadcrumb a {
                color: var(--mid);
                text-decoration: none;
                transition: color 0.15s;
            }
            .breadcrumb a:hover { color: var(--acid); }
            .breadcrumb span { color: var(--muted); }

            .topbar-right {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .topbar-btn {
                background: none;
                border: 1px solid var(--border);
                color: var(--muted);
                padding: 0.4rem 0.75rem;
                border-radius: 2px;
                font-family: var(--font-body);
                font-size: 0.75rem;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                transition: border-color 0.15s, color 0.15s;
            }
            .topbar-btn:hover { border-color: var(--acid); color: var(--acid); }

            .notif-btn {
                position: relative;
                background: none;
                border: 1px solid var(--border);
                width: 2.2rem; height: 2.2rem;
                border-radius: 2px;
                color: var(--muted);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                transition: border-color 0.15s, color 0.15s;
            }
            .notif-btn:hover { border-color: var(--acid); color: var(--acid); }
            .notif-dot {
                position: absolute;
                top: 0.2rem; right: 0.2rem;
                width: 0.45rem; height: 0.45rem;
                background: var(--acid);
                border-radius: 50%;
            }

            /* ─────────────────────────────── PAGE BODY ─── */
            .page-body {
                flex: 1;
                padding: 2rem;
                background: var(--black);
            }

            /* ─────────────────────────── STATS CARDS ─── */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1px;
                background: var(--border);
                border: 1px solid var(--border);
                border-radius: 4px;
                overflow: hidden;
                margin-bottom: 2rem;
            }
            .stat-card {
                background: var(--card);
                padding: 1.5rem;
                position: relative;
                overflow: hidden;
            }
            .stat-card::after {
                content: '';
                position: absolute;
                bottom: 0; left: 0; right: 0;
                height: 2px;
                background: var(--acid);
                transform: scaleX(0);
                transform-origin: left;
                transition: transform 0.3s;
            }
            .stat-card:hover::after { transform: scaleX(1); }
            .stat-label {
                font-size: 0.68rem;
                font-weight: 500;
                letter-spacing: 0.16em;
                text-transform: uppercase;
                color: var(--mid);
                margin-bottom: 0.75rem;
            }
            .stat-value {
                font-family: var(--font-display);
                font-size: 2.4rem;
                color: var(--white);
                line-height: 1;
                margin-bottom: 0.5rem;
            }
            .stat-value.acid { color: var(--acid); }
            .stat-change {
                font-size: 0.72rem;
                color: var(--mid);
                display: flex;
                align-items: center;
                gap: 0.3rem;
            }
            .stat-change.up   { color: var(--acid); }
            .stat-change.down { color: #f87171; }
            .stat-bg-icon {
                position: absolute;
                right: 1rem; bottom: 0.5rem;
                font-size: 3.5rem;
                opacity: 0.07;
                line-height: 1;
            }

            /* ─────────────────────────────── TABLE ─── */
            .table-card {
                background: var(--card);
                border: 1px solid var(--border);
                border-radius: 4px;
                overflow: hidden;
            }
            .table-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid var(--border);
                gap: 1rem;
                flex-wrap: wrap;
            }
            .table-card-title {
                font-family: var(--font-display);
                font-size: 1.2rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--white);
            }
            .table-card-actions {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                flex-wrap: wrap;
            }
            .search-input {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 2px;
                padding: 0.45rem 0.9rem;
                color: var(--white);
                font-family: var(--font-body);
                font-size: 0.8rem;
                outline: none;
                width: 200px;
                transition: border-color 0.15s;
            }
            .search-input::placeholder { color: var(--mid); }
            .search-input:focus { border-color: rgba(200,245,66,0.4); }

            .btn-add {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.45rem 1rem;
                background: var(--acid);
                color: var(--black);
                border: none;
                border-radius: 2px;
                font-family: var(--font-body);
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                cursor: pointer;
                text-decoration: none;
                transition: opacity 0.15s;
            }
            .btn-add:hover { opacity: 0.85; }

            .btn-outline {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.45rem 1rem;
                background: transparent;
                color: var(--muted);
                border: 1px solid var(--border);
                border-radius: 2px;
                font-family: var(--font-body);
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                cursor: pointer;
                text-decoration: none;
                transition: border-color 0.15s, color 0.15s;
            }
            .btn-outline:hover { border-color: var(--acid); color: var(--acid); }

            .data-table {
                width: 100%;
                border-collapse: collapse;
            }
            .data-table thead th {
                padding: 0.75rem 1.5rem;
                text-align: left;
                font-size: 0.65rem;
                font-weight: 500;
                letter-spacing: 0.16em;
                text-transform: uppercase;
                color: var(--mid);
                border-bottom: 1px solid var(--border);
                white-space: nowrap;
            }
            .data-table tbody td {
                padding: 1rem 1.5rem;
                font-size: 0.83rem;
                color: var(--muted);
                border-bottom: 1px solid var(--border);
                vertical-align: middle;
            }
            .data-table tbody tr:last-child td { border-bottom: none; }
            .data-table tbody tr { transition: background 0.12s; }
            .data-table tbody tr:hover { background: rgba(200,245,66,0.03); }

            .td-name {
                color: var(--white) !important;
                font-weight: 500;
            }

            /* Status badges */
            .badge {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.2rem 0.65rem;
                border-radius: 2px;
                font-size: 0.65rem;
                font-weight: 600;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .badge::before {
                content: '';
                width: 0.4rem; height: 0.4rem;
                border-radius: 50%;
                background: currentColor;
            }
            .badge-pending  { background: rgba(251,191,36,0.12); color: #fbbf24; }
            .badge-active   { background: rgba(200,245,66,0.12);  color: var(--acid); }
            .badge-returned { background: rgba(107,114,128,0.15); color: #9ca3af; }
            .badge-cancelled{ background: rgba(248,113,113,0.12); color: #f87171; }
            .badge-confirmed{ background: rgba(96,165,250,0.12);  color: #60a5fa; }

            .badge-admin    { background: rgba(200,245,66,0.12);  color: var(--acid); }
            .badge-petugas  { background: rgba(96,165,250,0.12);  color: #60a5fa; }
            .badge-user     { background: rgba(107,114,128,0.12); color: #9ca3af; }

            /* Action buttons */
            .action-group { display: flex; gap: 0.4rem; }
            .btn-action {
                padding: 0.3rem 0.65rem;
                border-radius: 2px;
                font-size: 0.7rem;
                font-weight: 500;
                letter-spacing: 0.05em;
                cursor: pointer;
                text-decoration: none;
                border: 1px solid;
                transition: background 0.12s, color 0.12s;
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                font-family: var(--font-body);
            }
            .btn-edit  { border-color: rgba(96,165,250,0.3); color: #60a5fa; background: transparent; }
            .btn-edit:hover  { background: rgba(96,165,250,0.1); }
            .btn-delete{ border-color: rgba(248,113,113,0.3); color: #f87171; background: transparent; }
            .btn-delete:hover{ background: rgba(248,113,113,0.1); }
            .btn-view  { border-color: var(--border); color: var(--muted); background: transparent; }
            .btn-view:hover  { border-color: var(--acid); color: var(--acid); }

            /* Table pagination */
            .table-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem 1.5rem;
                border-top: 1px solid var(--border);
                gap: 1rem;
                flex-wrap: wrap;
            }
            .table-info { font-size: 0.75rem; color: var(--mid); }
            .pagination {
                display: flex;
                gap: 0.25rem;
            }
            .page-btn {
                width: 2rem; height: 2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 2px;
                border: 1px solid var(--border);
                background: transparent;
                color: var(--muted);
                font-size: 0.78rem;
                cursor: pointer;
                text-decoration: none;
                transition: border-color 0.12s, color 0.12s, background 0.12s;
            }
            .page-btn:hover { border-color: var(--acid); color: var(--acid); }
            .page-btn.active { background: var(--acid); border-color: var(--acid); color: var(--black); font-weight: 700; }

            .table-responsive{
                width:100%;
                overflow-x:auto;
            }

            .table-responsive table{
                min-width:900px;
            }

            /* ─────────────────────── FORM ELEMENTS ─── */
            .form-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1.25rem;
            }
            .form-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
            .form-grid.cols-1 { grid-template-columns: 1fr; }

            .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
            .form-group.span-2 { grid-column: span 2; }

            .form-label {
                font-size: 0.68rem;
                font-weight: 500;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--mid);
            }
            .form-control {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 2px;
                padding: 0.6rem 0.9rem;
                color: var(--white);
                font-family: var(--font-body);
                font-size: 0.85rem;
                outline: none;
                width: 100%;
                transition: border-color 0.15s;
            }
            .form-control::placeholder { color: var(--mid); }
            .form-control:focus { border-color: rgba(200,245,66,0.5); }
            .form-control option { background: var(--card); }
            textarea.form-control { resize: vertical; min-height: 100px; }

            .form-hint {
                font-size: 0.72rem;
                color: var(--mid);
                margin-top: 0.15rem;
            }
            .form-error {
                font-size: 0.72rem;
                color: #f87171;
                margin-top: 0.15rem;
            }

            /* ─────────────── ALERT / FLASH MESSAGES ─── */
            .alert {
                padding: 0.9rem 1.2rem;
                border-radius: 4px;
                font-size: 0.82rem;
                display: flex;
                align-items: flex-start;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
                border-left: 3px solid;
            }
            .alert-success { background: rgba(200,245,66,0.07);  border-color: var(--acid);  color: var(--acid); }
            .alert-error   { background: rgba(248,113,113,0.07); border-color: #f87171; color: #f87171; }
            .alert-info    { background: rgba(96,165,250,0.07);  border-color: #60a5fa; color: #60a5fa; }
            .alert-warning { background: rgba(251,191,36,0.07);  border-color: #fbbf24; color: #fbbf24; }

            /* ─────────────────────────── MISC UTILS ─── */
            .section-title {
                font-family: var(--font-display);
                font-size: 1.5rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--white);
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .section-title::after {
                content: '';
                flex: 1;
                height: 1px;
                background: var(--border);
            }

            .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
            .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
            .mb-6 { margin-bottom: 1.5rem; }
            .mb-4 { margin-bottom: 1rem; }

            /* ─────────────────────────── RESPONSIVE ─── */
            @media (max-width: 1024px) {
                :root { --sidebar-w: 240px; }
                .stats-grid { grid-template-columns: repeat(2, 1fr); }
            }
            @media (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                }
                .sidebar.open {
                    transform: translateX(0);
                    box-shadow: 4px 0 24px rgba(0,0,0,0.5);
                }
                .sidebar-overlay {
                    display: none;
                    position: fixed;
                    inset: 0;
                    background: rgba(0,0,0,0.6);
                    z-index: 49;
                }
                .sidebar-overlay.open { display: block; }
                .main-content { margin-left: 0; }
                .sidebar-toggle { display: flex; }
                .stats-grid { grid-template-columns: 1fr 1fr; }
                .form-grid { grid-template-columns: 1fr; }
                .form-group.span-2 { grid-column: span 1; }
                .topbar { padding: 0 1rem; }
                .page-body { padding: 1.25rem; }
            }
            @media (max-width: 480px) {
                .stats-grid { grid-template-columns: 1fr; }
                .grid-2, .grid-3 { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <div class="admin-wrapper">

            <!-- ════════════════════════════ SIDEBAR ═════════════════════════ -->
            <aside class="sidebar" id="sidebar">

                <a href="{{ url('/') }}" class="sidebar-logo">
                    <span class="sidebar-logo-text">Sport<span>Rent</span></span>
                    <span class="sidebar-badge">
                        {{ auth()->user()?->role ?? 'Petugas' }}
                    </span>
                </a>

                <nav class="sidebar-nav">

                    <!-- UTAMA -->
                    <div class="nav-section-label">Utama</div>
                    <a href="{{ url('/petugas/dashboard') }}"
                       class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">⊞</span> Dashboard
                    </a>

                    <!-- KATALOG -->
                    <div class="nav-section-label">Katalog</div>
                    <a href="{{ url('/petugas/equipment') }}"
                       class="nav-item {{ request()->is('petugas/equipment*') ? 'active' : '' }}">
                        <span class="nav-icon">🏄</span> Alat Olahraga
                    </a>
                    <a href="{{ url('/petugas/categories') }}"
                       class="nav-item {{ request()->is('petugas/categories*') ? 'active' : '' }}">
                        <span class="nav-icon">◈</span> Kategori
                    </a>

                    <!-- TRANSAKSI -->
                    <!-- <div class="nav-section-label">Transaksi</div>
                    <a href="{{ url('/admin/rentals') }}"
                       class="nav-item {{ request()->is('admin/rentals*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span> Menunggu Konfirmasi
                    </a> -->
                    
                    <!-- <a href="{{ url('/admin/rentals/pending') }}"
                       class="nav-item {{ request()->is('admin/rentals/pending*') ? 'active' : '' }}">
                        <span class="nav-icon">⏳</span> Menunggu Konfirmasi
                        <span class="nav-badge">3</span>
                    </a> -->
                    
                    <a href="{{ url('/petugas/payments') }}"
                       class="nav-item {{ request()->is('petugas/payments*') ? 'active' : '' }}">
                        <span class="nav-icon">💳</span> Pembayaran
                    </a>

                    <!-- DATA -->
                    <!-- <div class="nav-section-label">Data</div>
                    <a href="{{ url('/admin/users') }}"
                       class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span> Pengguna
                    </a>
                    <a href="{{ url('/admin/reviews') }}"
                       class="nav-item {{ request()->is('admin/reviews*') ? 'active' : '' }}">
                        <span class="nav-icon">★</span> Ulasan
                    </a> -->

                    <!-- SISTEM (hanya admin) -->
                    @if(auth()->user()?->isAdmin())
                        <div class="nav-section-label">Sistem</div>
                        <a href="{{ url('/admin/settings') }}"
                           class="nav-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
                            <span class="nav-icon">⚙</span> Pengaturan
                        </a>
                    @endif

                </nav>

                <!-- User info di bawah sidebar -->
                <div class="sidebar-footer">
                    <div class="sidebar-user">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()?->name ?? 'Administrator' }}</div>
                            <div class="user-role {{ auth()->user()?->role ?? 'admin' }}">
                                {{ ucfirst(auth()->user()?->role ?? 'admin') }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0">
                            @csrf
                            <button type="submit" title="Logout"
                                    style="background:none;border:none;color:var(--mid);cursor:pointer;font-size:1rem;padding:0.2rem;transition:color 0.15s;"
                                    onmouseover="this.style.color='#f87171'"
                                    onmouseout="this.style.color='var(--mid)'">
                                ⏻
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Sidebar overlay (mobile) -->
            <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

            <!-- ════════════════════════════ MAIN ══════════════════════════════ -->
            <div class="main-content">

                <!-- Topbar -->
                <header class="topbar">
                    <div class="topbar-left">
                        <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

                        <div>
                            @isset($heading)
                                <div class="topbar-heading">{{ $heading }}</div>
                            @endisset
                            @isset($breadcrumb)
                                <div class="breadcrumb">{{ $breadcrumb }}</div>
                            @endisset
                        </div>
                    </div>

                    <div class="topbar-right">
                        <a href="{{ url('/') }}" target="_blank" class="topbar-btn">
                            ↗ Lihat Website
                        </a>
                        <button class="notif-btn" title="Notifikasi">
                            🔔
                            <span class="notif-dot"></span>
                        </button>
                    </div>
                </header>

                <!-- Flash messages -->
                <div style="padding: 0 2rem; padding-top: 1.5rem;">
                    @if(session('success'))
                        <div class="alert alert-success">✓ {{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-error">✕ {{ session('error') }}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning">⚠ {{ session('warning') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-error">
                            <div>
                                <strong>Terdapat beberapa kesalahan:</strong>
                                <ul style="margin-top:0.4rem;padding-left:1rem;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Page Content -->
                <main class="page-body">
                    @yield('content')
                </main>

            </div><!-- /.main-content -->
        </div><!-- /.admin-wrapper -->

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.toggle('open');
                overlay.classList.toggle('open');
            }

            // Auto-hide flash messages after 4 seconds
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(el => {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                });
            }, 4000);
        </script>
    </body>
</html>