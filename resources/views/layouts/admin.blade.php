<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — LensStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --ink: #0f172a;
            --ink-soft: #334155;
            --muted: #64748b;
            --canvas: #f8fafc;
            --surface: #ffffff;
            --surface-soft: #f1f5f9;
            --line: #e2e8f0;
            --line-strong: #cbd5e1;
            --accent: #f59e0b;
            --accent-soft: #fef3c7;
            --accent-hover: #d97706;
            --primary: #4f46e5;
            --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
            --teal: #0d9488;
            --teal-soft: #ccfbf1;
            --warn: #ea580c;
            --warn-soft: #ffedd5;
            --danger: #ef4444;
            --danger-soft: #fee2e2;
            --ok: #10b981;
            --ok-soft: #d1fae5;
            --info: #3b82f6;
            --sidebar-w: 272px;
            --radius: 14px;
            --radius-sm: 10px;
            --shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -2px rgba(0,0,0,.1);
            --shadow-hover: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -2px rgba(0,0,0,.05);
            --font: 'Inter', sans-serif;
            --display: 'Inter', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; }
        body {
            font-family: var(--font);
            background: var(--canvas);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font-family: inherit; }

        /* ——— Sidebar ——— */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--dark, #0f172a);
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 50;
            border-right: 1px solid rgba(255,255,255,.05);
        }

        .sidebar-brand {
            padding: 1.4rem 1.35rem 1.2rem;
            display: flex;
            align-items: center;
            gap: .85rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(79,70,229,.35);
            animation: brandPulse 4s ease-in-out infinite;
        }

        @keyframes brandPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .brand-text strong {
            font-family: var(--display);
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.02em;
        }

        .brand-text small {
            font-size: .68rem;
            color: #6d7788;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-top: .15rem;
        }

        .sidebar-nav {
            padding: .9rem .75rem;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #5c6675;
            padding: .9rem .7rem .4rem;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .72rem .85rem;
            color: #a8b1bf;
            font-size: .9rem;
            font-weight: 500;
            border-radius: 11px;
            margin-bottom: 2px;
            transition: background .2s, color .2s, transform .2s;
            position: relative;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: .9rem;
            opacity: .75;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.05);
            color: #fff;
            transform: translateX(2px);
        }

        .nav-item.active {
            background: rgba(79,70,229,.15);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(79,70,229,.25);
        }

        .nav-item.active i { color: var(--primary); opacity: 1; }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--primary);
        }

        .sidebar-footer {
            padding: 1rem 1.1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.06);
            background: rgba(0,0,0,.15);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: .85rem;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: var(--primary);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: .85rem;
            border: 1px solid rgba(255,255,255,.1);
        }

        .user-chip .user-name { color: #fff; font-weight: 600; font-size: .88rem; }
        .user-chip .user-role {
            color: #7a8494;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 600;
        }

        .sidebar-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .45rem;
        }

        .sidebar-actions a,
        .sidebar-actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .5rem;
            border-radius: 9px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.04);
            color: #c5ccd6;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .sidebar-actions a:hover,
        .sidebar-actions button:hover {
            background: rgba(255,255,255,.09);
            color: #fff;
        }

        /* ——— Main ——— */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            padding: 1.15rem 2rem .35rem;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            animation: fadeDown .45s ease both;
        }

        .topbar h1 {
            font-family: var(--display);
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: -.03em;
            color: var(--ink);
            line-height: 1.2;
        }

        .topbar-sub {
            font-size: .82rem;
            color: var(--muted);
            margin-top: .2rem;
            font-weight: 500;
        }

        .topbar-actions { display: flex; gap: .6rem; align-items: center; flex-wrap: wrap; }

        .content {
            padding: 1.1rem 2rem 2.75rem;
            animation: fadeUp .5s ease .05s both;
            flex: 1;
            min-width: 0;
        }
        body.is-chat-page {
            overflow: hidden;
        }
        body.is-chat-page .main {
            height: 100vh;
            min-height: 100vh;
            overflow: hidden;
        }
        body.is-chat-page .content {
            padding: .35rem 1.25rem 1.15rem;
            display: flex;
            min-height: 0;
            overflow: hidden;
        }
        body.is-chat-page .topbar { padding-bottom: .55rem; flex-shrink: 0; }
        .nav-chat-badge, .nav-badge {
            margin-left: auto;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
        }
        .nav-chat-badge { display: none; }
        .nav-chat-badge.show { display: inline-flex; }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: none; }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: none; }
        }

        /* ——— Buttons ——— */
        .btn,
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .58rem 1.05rem;
            border-radius: 11px;
            border: 1px solid transparent;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            background: var(--primary);
            color: #fff !important;
            transition: transform .15s, background .15s, box-shadow .15s;
            box-shadow: 0 4px 12px rgba(79,70,229,.22);
        }

        .btn:hover,
        .btn-primary:hover {
            background: var(--primary-hover, #4338ca);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79,70,229,.28);
        }

        .btn-outline,
        .btn-secondary {
            background: var(--surface) !important;
            border-color: var(--line) !important;
            color: var(--ink) !important;
            box-shadow: none;
        }

        .btn-outline:hover,
        .btn-secondary:hover {
            background: var(--surface-soft) !important;
            border-color: var(--line-strong) !important;
            box-shadow: none;
        }

        .btn-danger { background: var(--danger) !important; color: #fff !important; box-shadow: 0 4px 12px rgba(239,68,68,.25); }
        .btn-danger:hover { background: #dc2626 !important; transform: translateY(-1px); }
        .btn-success { background: var(--ok, #10b981) !important; color: #fff !important; box-shadow: 0 4px 12px rgba(16,185,129,.25); }
        .btn-success:hover { background: #059669 !important; transform: translateY(-1px); }
        .btn-sm { padding: .38rem .7rem; font-size: .78rem; border-radius: 9px; }

        /* ——— Cards / stats ——— */
        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.35rem;
            transition: box-shadow .25s, transform .25s;
            min-width: 0;
        }

        .card:hover { box-shadow: var(--shadow-hover); }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: .75rem;
        }

        .card-title {
            font-family: var(--display);
            font-size: 1.02rem;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.35rem;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.2rem 1.25rem;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
            animation: fadeUp .5s ease both;
        }

        .stat-card:nth-child(1) { animation-delay: .05s; }
        .stat-card:nth-child(2) { animation-delay: .1s; }
        .stat-card:nth-child(3) { animation-delay: .15s; }
        .stat-card:nth-child(4) { animation-delay: .2s; }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            right: -20px;
            top: -20px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: var(--accent-soft);
            opacity: .55;
        }

        .stat-card .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: var(--primary);
            color: #fff;
            font-size: .85rem;
            margin-bottom: .75rem;
            position: relative;
            z-index: 1;
        }

        .stat-card.tone-teal .stat-icon { background: var(--teal-soft); color: var(--teal); }
        .stat-card.tone-teal::after { background: var(--teal-soft); }
        .stat-card.tone-warn .stat-icon { background: var(--warn-soft); color: var(--warn); }
        .stat-card.tone-warn::after { background: var(--warn-soft); }
        .stat-card.tone-ok .stat-icon { background: var(--ok-soft); color: var(--ok); }
        .stat-card.tone-ok::after { background: var(--ok-soft); }
        .stat-card.tone-danger .stat-icon { background: var(--danger-soft); color: var(--danger); }
        .stat-card.tone-danger::after { background: var(--danger-soft); }

        .stat-card .label {
            font-size: .78rem;
            color: var(--muted);
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .stat-card .value {
            font-family: var(--display);
            font-size: 1.55rem;
            font-weight: 700;
            margin-top: .2rem;
            letter-spacing: -.03em;
            position: relative;
            z-index: 1;
        }

        .stat-card .hint {
            font-size: .75rem;
            color: var(--muted);
            margin-top: .35rem;
            position: relative;
            z-index: 1;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 1.1rem;
            margin-bottom: 1.35rem;
            min-width: 0;
        }

        /* ——— Tables ——— */
        .table-wrap {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--line);
        }

        .card > .table-wrap:not(:first-child) {
            border: none;
            border-radius: 0;
            margin: 0 -1.35rem -1.35rem;
            border-top: 1px solid var(--line);
        }
        .card.flush { overflow: hidden; }
        .card.flush .table-wrap { margin: 0; border: none; border-radius: 0; }

        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: .875rem;
        }

        table.data th,
        table.data td {
            padding: .85rem 1.1rem;
            text-align: left;
            border-bottom: 1px solid var(--line);
        }

        table.data th {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            background: var(--surface-soft);
            font-weight: 700;
            white-space: nowrap;
        }

        table.data tbody tr {
            transition: background .15s;
        }

        table.data tbody tr:hover td { background: #fafbfd; }
        table.data tbody tr:last-child td { border-bottom: none; }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: .28rem .65rem;
            border-radius: 8px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .01em;
        }

        /* ——— Alerts / forms ——— */
        .alert {
            padding: .9rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.1rem;
            font-size: .9rem;
            font-weight: 500;
            animation: fadeDown .35s ease;
        }

        .alert-success { background: var(--ok-soft); color: var(--ok); border: 1px solid #b7e4c7; }
        .alert-error { background: var(--danger-soft); color: var(--danger); border: 1px solid #f5c2be; }
        .alert-warn { background: var(--warn-soft); color: var(--warn); border: 1px solid #fcd9a8; }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            margin-bottom: 1.15rem;
            align-items: end;
            padding: 1rem 1.15rem;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .filters label,
        .form-group label {
            display: block;
            font-size: .72rem;
            color: var(--muted);
            margin-bottom: .3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .filters input,
        .filters select,
        .form-group input,
        .form-group select,
        .form-group textarea,
        .form-control {
            padding: .55rem .75rem;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: .875rem;
            background: #fff;
            color: var(--ink);
            transition: border-color .15s, box-shadow .15s;
            width: auto;
            max-width: 100%;
        }

        .form-group input,
        .form-group select,
        .form-group textarea { width: 100%; }

        .filters input:focus,
        .filters select:focus,
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus,
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,.15);
        }

        .form-group { margin-bottom: 1.05rem; }

        .pagination { margin-top: 1.1rem; display: flex; gap: .4rem; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: .4rem .7rem;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: .8rem;
            background: #fff;
            font-weight: 600;
        }
        .pagination .active span {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .muted { color: var(--muted); }
        .text-ok { color: var(--ok); }
        .text-warn { color: var(--warn); }
        .text-danger { color: var(--danger); }
        .actions { display: flex; gap: .4rem; flex-wrap: wrap; }
        .chart-box { height: 290px; position: relative; }

        /* Legacy page-header: keep actions visible, hide duplicate H1 */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .page-header > h1,
        .page-header > h2 { display: none; }
        .justify-content-between > h1,
        .justify-content-between > h2 { display: none; }
        .mb-0 { margin-bottom: 0 !important; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .gap-2 { gap: .5rem; }
        .gap-4 { gap: 1rem; }
        .form-control { display: inline-block; }
        .mb-4 { margin-bottom: 1.15rem; }
        .mt-4 { margin-top: 1.15rem; }
        .text-muted, .text-main { color: var(--muted); }
        .text-primary { color: var(--accent); }
        .action-buttons { display: flex; gap: .35rem; flex-wrap: wrap; }
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; width: 100%; }
        .align-items-center { align-items: center; }
        .table-responsive { overflow-x: auto; }
        table.table,
        .card > .table-responsive > table,
        .card table:not(.data) {
            width: 100%;
            border-collapse: collapse;
            font-size: .875rem;
        }
        table.table th, table.table td,
        .card > .table-responsive > table th,
        .card > .table-responsive > table td,
        .card table:not(.data) th,
        .card table:not(.data) td {
            padding: .85rem 1.1rem;
            text-align: left;
            border-bottom: 1px solid var(--line);
        }
        table.table th,
        .card > .table-responsive > table th,
        .card table:not(.data) th {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            background: var(--surface-soft);
            font-weight: 700;
        }
        .card > .table-responsive > table tbody tr:hover td,
        .card table:not(.data) tbody tr:hover td { background: #fafbfd; }

        .status-tabs {
            display: flex;
            gap: .45rem;
            flex-wrap: wrap;
            margin-bottom: 1.1rem;
        }

        .status-tabs a {
            padding: .48rem .9rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            background: var(--surface);
            border: 1px solid var(--line);
            color: var(--ink-soft);
            transition: .2s;
        }

        .status-tabs a:hover { border-color: var(--line-strong); color: var(--ink); }
        .status-tabs a.is-active {
            background: var(--ink);
            border-color: var(--ink);
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 1.6rem;
            margin-bottom: .6rem;
            opacity: .45;
            display: block;
        }

        @media (max-width: 1100px) {
            .grid-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 860px) {
            :root { --sidebar-w: 78px; }
            .brand-text, .nav-section, .nav-item span, .user-chip div:last-child, .sidebar-actions { display: none; }
            .sidebar-brand { justify-content: center; padding: 1.1rem .5rem; }
            .nav-item { justify-content: center; padding: .85rem; }
            .nav-item.active::before { display: none; }
            .nav-chat-badge { display: none !important; }
            .user-chip { justify-content: center; }
            .topbar, .content { padding-left: 1.15rem; padding-right: 1.15rem; }
            .topbar h1 { font-size: 1.3rem; }
        }
    </style>
    @stack('styles')
</head>
<body class="@yield('body-class')">
    @php
        $userRoles = auth()->user() ? auth()->user()->getRoleNames() : collect();
        $roleStr = $userRoles->isNotEmpty() ? $userRoles->implode(', ') : 'Chưa phân quyền';
        $initials = collect(explode(' ', auth()->user()->name ?? 'A'))
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->take(2)
            ->implode('');
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $pendingReturns = \App\Models\ReturnRequest::where('status', 'pending')->count();
    @endphp
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark"><i class="fa-solid fa-aperture"></i></div>
            <div class="brand-text">
                <strong>LensStore</strong>
                <small>Admin Panel</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Tổng quan</div>
            @can('view_dashboard')
            <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-chart-pie"></i> <span>Dashboard</span>
            </a>
            @endcan
            <a class="nav-item {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
                <i class="fa-solid fa-comments"></i>
                <span>Live chat</span>
                <span class="nav-chat-badge" id="admin-chat-nav-badge">0</span>
            </a>

            @if(auth()->user()->can('manage_orders') || auth()->user()->can('manage_products') || auth()->user()->can('manage_categories'))
            <div class="nav-section">Kinh doanh</div>
            @can('manage_orders')
            <a class="nav-item {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.returns.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                <i class="fa-solid fa-boxes-packing"></i> <span>Đơn hàng</span>
                @if($pendingOrders > 0)
                <span class="nav-badge">{{ $pendingOrders > 9 ? '9+' : $pendingOrders }}</span>
                @endif
            </a>
            <a class="nav-item {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}" href="{{ route('admin.returns.index') }}">
                <i class="fa-solid fa-rotate-left"></i> <span>Yêu cầu trả hàng</span>
                @if($pendingReturns > 0)
                <span class="nav-badge">{{ $pendingReturns > 9 ? '9+' : $pendingReturns }}</span>
                @endif
            </a>
            <a class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                <i class="fa-solid fa-star"></i> <span>Đánh giá SP</span>
            </a>
            @endcan
            @can('manage_products')
            <a class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                <i class="fa-solid fa-camera"></i> <span>Sản phẩm</span>
            </a>
            @endcan

            @if(auth()->user()->can('manage_goods_receipts') || auth()->user()->can('manage_goods_issues') || auth()->user()->can('manage_qc_inspections'))
            <div class="nav-section">Quản lý Kho hàng</div>
            @can('manage_goods_receipts')
            <a class="nav-item {{ request()->routeIs('admin.goods_receipts.*') ? 'active' : '' }}" href="{{ route('admin.goods_receipts.index') }}">
                <i class="fa-solid fa-truck-ramp-box"></i> <span>Nhập kho</span>
            </a>
            @endcan
            @can('manage_goods_issues')
            <a class="nav-item {{ request()->routeIs('admin.goods_issues.*') ? 'active' : '' }}" href="{{ route('admin.goods_issues.index') }}">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Xuất kho</span>
            </a>
            @endcan
            @can('manage_qc_inspections')
            <a class="nav-item {{ request()->routeIs('admin.qc_inspections.*') ? 'active' : '' }}" href="{{ route('admin.qc_inspections.index') }}">
                <i class="fa-solid fa-microscope"></i> <span>Kiểm định Hàng trả (QC)</span>
            </a>
            @endcan
            @endif

            @can('manage_categories')
            <a class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <i class="fa-solid fa-layer-group"></i> <span>Danh mục</span>
            </a>
            @endcan
            @endif

            @if(auth()->user()->can('manage_customers') || auth()->user()->can('manage_vouchers') || auth()->user()->can('manage_users') || auth()->user()->can('manage_roles'))
            <div class="nav-section">Hệ thống & Khách hàng</div>
            @can('manage_customers')
            <a class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                <i class="fa-solid fa-address-card"></i> <span>Khách hàng CRM</span>
            </a>
            @endcan
            @can('manage_vouchers')
            <a class="nav-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}" href="{{ route('admin.vouchers.index') }}">
                <i class="fa-solid fa-ticket"></i> <span>Voucher</span>
            </a>
            @endcan
            @can('manage_users')
            <a class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="fa-solid fa-user-group"></i> <span>Tài khoản</span>
            </a>
            @endcan
            @can('manage_roles')
            <a class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                <i class="fa-solid fa-shield-halved"></i> <span>Phân quyền</span>
            </a>
            @endcan
            @endif

            @if(auth()->user()->can('manage_news'))
            <div class="nav-section">Nội dung</div>
            <a class="nav-item {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">
                <i class="fa-solid fa-newspaper"></i> <span>Tin tức</span>
            </a>
            @endif

            @can('view_reports')
            <div class="nav-section">Phân tích</div>
            <a class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                <i class="fa-solid fa-chart-column"></i> <span>Báo cáo</span>
            </a>
            @endcan
        </nav>
        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: inherit; object-fit: cover;">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ $roleStr }}</div>
                </div>
            </div>
            <div class="sidebar-actions">
                <a href="{{ route('storefront.index') }}"><i class="fa-solid fa-store"></i> Shop</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="width:100%"><i class="fa-solid fa-arrow-right-from-bracket"></i> Thoát</button>
                </form>
            </div>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <h1>@yield('title', 'Dashboard')</h1>
                @hasSection('subtitle')
                    <div class="topbar-sub">@yield('subtitle')</div>
                @endif
            </div>
            <div class="topbar-actions">
                @yield('actions')
            </div>
        </header>
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin-left:1rem">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    @stack('scripts')
    @auth
    @if(\App\Support\ChatSupport::isStaff(auth()->user()) && !request()->routeIs('admin.chat.index'))
    <script>
    (function () {
        const badge = document.getElementById('admin-chat-nav-badge');
        if (!badge) return;
        async function ping() {
            try {
                const res = await fetch(@json(route('admin.chat.users')), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                const n = Number(data.unread) || 0;
                badge.textContent = n > 9 ? '9+' : String(n);
                badge.classList.toggle('show', n > 0);
            } catch (e) {}
        }
        ping();
        setInterval(ping, 8000);
    })();
    </script>
    @endif
    @endauth
</body>
</html>
